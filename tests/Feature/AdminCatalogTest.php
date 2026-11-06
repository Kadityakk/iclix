<?php

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');

    $this->actingAs(User::factory()->admin()->create());
});

/**
 * @return array<string, mixed>
 */
function validMoviePayload(array $overrides = []): array
{
    return $overrides + [
        'type' => 'movie',
        'title' => 'Film Baru Sekali',
        'synopsis' => 'Sinopsis film baru.',
        'release_year' => 2026,
        'release_date' => '2026-05-17',
        'tagline' => 'Sebuah tagline',
        'duration_min' => 100,
        'age_rating' => '13+',
        'cast_members' => 'Pemeran Satu, Pemeran Dua',
        'trailer_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
        'genres' => [Genre::factory()->create()->id],
        'poster' => UploadedFile::fake()->image('poster.jpg'),
        'backdrop' => UploadedFile::fake()->image('backdrop.png'),
        'video' => UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4'),
    ];
}

test('the dashboard shows member, subscription and content totals plus the top five', function () {
    Subscription::factory()->create();
    Subscription::factory()->expired()->create();
    Content::factory()->count(6)->sequence(fn ($sequence) => ['views' => $sequence->index])->create();

    $response = $this->get(route('admin.dashboard'))->assertOk();

    expect($response->viewData('memberCount'))->toBe(2)
        ->and($response->viewData('activeSubscriptionCount'))->toBe(1)
        ->and($response->viewData('contentCount'))->toBe(6)
        ->and($response->viewData('topContents')->pluck('views')->all())->toBe([5, 4, 3, 2, 1]);
});

test('a movie can be created with uploads and appears on the member home page', function () {
    $this->post(route('admin.contents.store'), validMoviePayload(['is_featured' => '1']))
        ->assertRedirect(route('admin.contents.index'));

    $content = Content::query()->sole();

    expect($content->slug)->toBe('film-baru-sekali')
        ->and($content->type)->toBe(ContentType::Movie)
        ->and($content->cast_members)->toBe(['Pemeran Satu', 'Pemeran Dua'])
        ->and($content->is_featured)->toBeTrue()
        ->and($content->genres)->toHaveCount(1);

    Storage::disk('public')->assertExists([$content->poster, $content->backdrop]);
    Storage::disk('local')->assertExists($content->video_path);
    Storage::disk('public')->assertMissing($content->video_path);

    [, $profile] = subscribedMember();
    watchingAs($profile)->get(route('browse'))->assertOk()->assertSee('Film Baru Sekali');
});

test('a series is created without a video and opens its seasons', function () {
    $payload = validMoviePayload(['type' => 'series', 'title' => 'Serial Baru']);
    unset($payload['video'], $payload['duration_min']);

    $response = $this->post(route('admin.contents.store'), $payload);
    $content = Content::query()->sole();

    $response->assertRedirect(route('admin.contents.seasons.index', $content));
    expect($content->video_path)->toBeNull()->and($content->duration_min)->toBeNull();
});

test('empty content submissions report every required field', function () {
    $this->post(route('admin.contents.store'), ['type' => 'movie'])
        ->assertSessionHasErrors(['title', 'synopsis', 'release_year', 'duration_min', 'age_rating', 'genres', 'poster', 'video']);
});

test('uploads outside the allowed type or size are rejected', function (string $field, UploadedFile $file) {
    $this->post(route('admin.contents.store'), validMoviePayload([$field => $file]))
        ->assertSessionHasErrors($field);

    expect(Content::query()->count())->toBe(0);
})->with([
    'poster over 2 MB' => fn () => ['poster', UploadedFile::fake()->image('poster.jpg')->size(2049)],
    'poster as svg' => fn () => ['poster', UploadedFile::fake()->create('poster.svg', 10, 'image/svg+xml')],
    'backdrop as gif' => fn () => ['backdrop', UploadedFile::fake()->create('backdrop.gif', 10, 'image/gif')],
    'video over 500 MB' => fn () => ['video', UploadedFile::fake()->create('video.mp4', 512001, 'video/mp4')],
    'video as avi' => fn () => ['video', UploadedFile::fake()->create('video.avi', 10, 'video/x-msvideo')],
]);

test('trailers must be youtube links', function () {
    $this->post(route('admin.contents.store'), validMoviePayload(['trailer_url' => 'https://evil.example/watch?v=aqz-KE-bpKQ']))
        ->assertSessionHasErrors(['trailer_url' => 'Trailer harus berupa tautan video YouTube.']);
});

test('a duplicate slug is rejected', function () {
    Content::factory()->create(['slug' => 'film-baru-sekali']);

    $this->post(route('admin.contents.store'), validMoviePayload())->assertSessionHasErrors('slug');
});

test('updating without new files keeps the existing ones', function () {
    $content = Content::factory()->create(['poster' => 'posters/old.jpg', 'video_path' => 'videos/old.mp4']);
    $payload = validMoviePayload(['title' => 'Judul Diperbarui']);
    unset($payload['poster'], $payload['backdrop'], $payload['video'], $payload['type']);

    $this->put(route('admin.contents.update', $content), $payload)->assertSessionHasNoErrors();

    $content->refresh();

    expect($content->title)->toBe('Judul Diperbarui')
        ->and($content->poster)->toBe('posters/old.jpg')
        ->and($content->video_path)->toBe('videos/old.mp4');
});

test('replacing a file removes the old one', function () {
    Storage::disk('public')->put('posters/old.jpg', 'old');
    $content = Content::factory()->create(['poster' => 'posters/old.jpg']);
    $payload = validMoviePayload();
    unset($payload['backdrop'], $payload['video'], $payload['type']);

    $this->put(route('admin.contents.update', $content), $payload)->assertSessionHasNoErrors();

    Storage::disk('public')->assertMissing('posters/old.jpg');
    Storage::disk('public')->assertExists($content->refresh()->poster);
});

test('deleting content removes its files and episodes but keeps shared demo files', function () {
    Storage::disk('public')->put('posters/own.jpg', 'x');
    Storage::disk('local')->put('videos/episode.mp4', 'x');
    Storage::disk('local')->put('demo/sample.mp4', 'x');
    $series = Content::factory()->series()->create(['poster' => 'posters/own.jpg']);
    $season = Season::factory()->for($series)->create();
    $episode = Episode::factory()->for($season)->create(['video_path' => 'videos/episode.mp4']);
    $demoEpisode = Episode::factory()->for($season)->create(['video_path' => 'demo/sample.mp4']);

    $this->delete(route('admin.contents.destroy', $series))->assertRedirect(route('admin.contents.index'));

    expect(Content::query()->count())->toBe(0)
        ->and($episode->fresh())->toBeNull()
        ->and($demoEpisode->fresh())->toBeNull();

    Storage::disk('public')->assertMissing('posters/own.jpg');
    Storage::disk('local')->assertMissing('videos/episode.mp4');
    Storage::disk('local')->assertExists('demo/sample.mp4');
});

test('the featured flag can be toggled', function () {
    $content = Content::factory()->create();

    $this->patch(route('admin.contents.featured', $content));
    expect($content->refresh()->is_featured)->toBeTrue();

    $this->patch(route('admin.contents.featured', $content));
    expect($content->refresh()->is_featured)->toBeFalse();
});

test('the content list searches by title', function () {
    Content::factory()->create(['title' => 'Cari Saya']);
    Content::factory()->create(['title' => 'Yang Lain']);

    $this->get(route('admin.contents.index', ['q' => 'cari']))->assertOk()->assertSee('Cari Saya')->assertDontSee('Yang Lain');
});

test('seasons and episodes can be managed for a series', function () {
    $series = Content::factory()->series()->create();

    $this->post(route('admin.contents.seasons.store', $series), ['season_number' => 1])
        ->assertRedirect(route('admin.contents.seasons.index', $series));
    $season = $series->seasons()->sole();

    $this->post(route('admin.seasons.episodes.store', $season), [
        'episode_number' => 1,
        'title' => 'Pilot',
        'duration_min' => 45,
        'video' => UploadedFile::fake()->create('episode.mp4', 100, 'video/mp4'),
    ])->assertRedirect(route('admin.seasons.episodes.index', $season));

    $episode = $season->episodes()->sole();
    Storage::disk('local')->assertExists($episode->video_path);

    $this->put(route('admin.episodes.update', $episode), ['episode_number' => 2, 'title' => 'Pilot Baru', 'duration_min' => 50])
        ->assertSessionHasNoErrors();
    expect($episode->refresh()->title)->toBe('Pilot Baru');

    $path = $episode->video_path;
    $this->delete(route('admin.episodes.destroy', $episode));
    Storage::disk('local')->assertMissing($path);
});

test('season and episode numbers are unique within their parent', function () {
    $season = Season::factory()->create(['season_number' => 1]);
    Episode::factory()->for($season)->create(['episode_number' => 1]);

    $this->post(route('admin.contents.seasons.store', $season->content_id), ['season_number' => 1])
        ->assertSessionHasErrors('season_number');

    $this->post(route('admin.seasons.episodes.store', $season), [
        'episode_number' => 1,
        'title' => 'Duplikat',
        'duration_min' => 45,
        'video' => UploadedFile::fake()->create('episode.mp4', 100, 'video/mp4'),
    ])->assertSessionHasErrors('episode_number');
});

test('movies have no seasons', function () {
    $movie = Content::factory()->create();

    $this->get(route('admin.contents.seasons.index', $movie))->assertNotFound();
    $this->post(route('admin.contents.seasons.store', $movie), ['season_number' => 1])->assertNotFound();
});

test('admin pages render', function (string $routeName) {
    $this->get(route($routeName))->assertOk();
})->with([
    'admin.contents.index',
    'admin.contents.create',
    'admin.genres.index',
    'admin.genres.create',
    'admin.plans.index',
    'admin.plans.create',
    'admin.users.index',
]);

test('nested admin forms render', function () {
    $season = Season::factory()->create();
    $episode = Episode::factory()->for($season)->create();

    $this->get(route('admin.contents.edit', $season->content_id))->assertOk();
    $this->get(route('admin.contents.seasons.create', $season->content_id))->assertOk();
    $this->get(route('admin.seasons.edit', $season))->assertOk();
    $this->get(route('admin.seasons.episodes.index', $season))->assertOk();
    $this->get(route('admin.seasons.episodes.create', $season))->assertOk();
    $this->get(route('admin.episodes.edit', $episode))->assertOk();
});
