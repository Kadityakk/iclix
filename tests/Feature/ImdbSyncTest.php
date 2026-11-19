<?php

use App\Enums\AgeRating;
use App\Models\Content;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Http::preventStrayRequests();
    config(['services.omdb.key' => 'test-key']);
});

test('the sync command copies the IMDb title, plot, poster, rating and details onto the content', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbTitle())]);
    $drama = Genre::factory()->create(['name' => 'Drama']);
    $sciFi = Genre::factory()->create(['name' => 'Sci-Fi']);
    $content = Content::factory()->create([
        'imdb_id' => 'tt0816692',
        'slug' => 'judul-lama',
        'title' => 'Judul Lama',
        'synopsis' => 'Sinopsis lama.',
        'imdb_rating' => 7.1,
        'age_rating' => AgeRating::AllAges,
        'poster' => 'https://m.media-amazon.com/images/M/old@._V1_.jpg',
        'duration_min' => 90,
        'cast_members' => ['Pemeran Lama'],
    ]);
    $content->genres()->attach(Genre::factory()->create());

    $this->artisan('imdb:sync')->assertSuccessful();

    $content->refresh();

    expect($content)
        ->title->toBe('Interstellar')
        ->slug->toBe('judul-lama')
        ->synopsis->toBe('A team of explorers travel through a wormhole in space.')
        ->imdb_rating->toBe(8.7)
        ->poster->toBe(IMDB_POSTER_RESIZED)
        ->release_year->toBe(2014)
        ->release_date->toDateString()->toBe('2014-11-07')
        ->duration_min->toBe(169)
        ->age_rating->toBe(AgeRating::ThirteenPlus)
        ->cast_members->toBe(['Matthew McConaughey', 'Anne Hathaway', 'Jessica Chastain'])
        ->and($content->genres->pluck('id')->sort()->values()->all())->toBe([$drama->id, $sciFi->id]);

    Http::assertSent(fn ($request) => $request['i'] === 'tt0816692' && $request['apikey'] === 'test-key');
});

test('the sync command keeps an uploaded poster and fields IMDb leaves empty', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbTitle([
        'Plot' => 'N/A',
        'Rated' => 'Not Rated',
        'Runtime' => 'N/A',
        'Genre' => 'Western',
    ]))]);
    $genre = Genre::factory()->create();
    $content = Content::factory()->create([
        'imdb_id' => 'tt0816692',
        'poster' => 'posters/upload.jpg',
        'synopsis' => 'Sinopsis dari admin.',
        'age_rating' => AgeRating::SeventeenPlus,
        'duration_min' => 120,
    ]);
    $content->genres()->attach($genre);

    $this->artisan('imdb:sync')->assertSuccessful();

    $content->refresh();

    expect($content)
        ->poster->toBe('posters/upload.jpg')
        ->synopsis->toBe('Sinopsis dari admin.')
        ->age_rating->toBe(AgeRating::SeventeenPlus)
        ->duration_min->toBe(120)
        ->and($content->genres->pluck('id')->all())->toBe([$genre->id]);
});
test('the sync command fails and leaves the content untouched when IMDb does not know the title', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(['Response' => 'False', 'Error' => 'Incorrect IMDb ID.'])]);
    $content = Content::factory()->create(['imdb_id' => 'tt0000001', 'imdb_rating' => 6.0]);

    $this->artisan('imdb:sync')->assertFailed();

    expect($content->fresh()->imdb_rating)->toBe(6.0);
});

test('the sync command refuses to run without an OMDb API key', function () {
    config(['services.omdb.key' => null]);
    Content::factory()->create(['imdb_id' => 'tt0816692']);

    $this->artisan('imdb:sync')->assertFailed();

    Http::assertNothingSent();
});

test('an admin can add a title by IMDb ID without uploading a poster', function () {
    Storage::fake('public');
    Storage::fake('local');
    Http::fake([OMDB_ENDPOINT => Http::response(omdbTitle())]);
    $this->actingAs(User::factory()->admin()->create());

    $this->post(route('admin.contents.store'), [
        'type' => 'movie',
        'imdb_id' => 'tt0816692',
        'title' => 'Interstellar',
        'synopsis' => 'Penjelajah menembus lubang cacing.',
        'release_year' => 2014,
        'duration_min' => 169,
        'age_rating' => '13+',
        'genres' => [Genre::factory()->create()->id],
        'video' => UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4'),
    ])->assertRedirect(route('admin.contents.index'))->assertSessionHas('status', 'Interstellar berhasil ditambahkan.');

    expect(Content::query()->sole())
        ->imdb_id->toBe('tt0816692')
        ->imdb_rating->toBe(8.7)
        ->posterUrl()->toBe(IMDB_POSTER_RESIZED);
});

test('saving content still succeeds when IMDb is unavailable, with a note for the admin', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(['Response' => 'False', 'Error' => 'Request limit reached!'])]);
    $this->actingAs(User::factory()->admin()->create());
    $content = Content::factory()->create(['imdb_id' => null]);

    $this->put(route('admin.contents.update', $content), [
        'imdb_id' => 'tt0816692',
        'title' => $content->title,
        'synopsis' => $content->synopsis,
        'release_year' => $content->release_year,
        'duration_min' => $content->duration_min,
        'age_rating' => $content->age_rating->value,
        'genres' => [Genre::factory()->create()->id],
    ])->assertSessionHas('status', "{$content->title} berhasil diperbarui. Data IMDb belum diambil: Request limit reached!");

    expect($content->fresh())->imdb_id->toBe('tt0816692')->imdb_rating->toBeNull();
});

test('an IMDb ID must look like tt followed by digits and be unique', function (string $imdbId) {
    Content::factory()->create(['imdb_id' => 'tt0816692']);
    $this->actingAs(User::factory()->admin()->create());

    $this->post(route('admin.contents.store'), ['type' => 'movie', 'imdb_id' => $imdbId])
        ->assertSessionHasErrors('imdb_id');

    Http::assertNothingSent();
})->with([
    'not an IMDb ID' => 'interstellar',
    'already used' => 'tt0816692',
]);

test('clearing the IMDb ID removes the stale rating', function () {
    $this->actingAs(User::factory()->admin()->create());
    $content = Content::factory()->create(['imdb_id' => 'tt0816692', 'imdb_rating' => 8.7]);

    $this->put(route('admin.contents.update', $content), [
        'imdb_id' => '',
        'title' => $content->title,
        'synopsis' => $content->synopsis,
        'release_year' => $content->release_year,
        'duration_min' => $content->duration_min,
        'age_rating' => $content->age_rating->value,
        'genres' => [Genre::factory()->create()->id],
    ])->assertSessionHasNoErrors();

    expect($content->fresh())->imdb_id->toBeNull()->imdb_rating->toBeNull();
    Http::assertNothingSent();
});

test('members see the IMDb rating on the title page', function () {
    [, $profile] = subscribedMember();
    $content = Content::factory()->create(['imdb_id' => 'tt0816692', 'imdb_rating' => 8.7]);

    watchingAs($profile)->get(route('contents.show', $content))
        ->assertOk()
        ->assertSee('8.7')
        ->assertSee('https://www.imdb.com/title/tt0816692/', escape: false);
});
