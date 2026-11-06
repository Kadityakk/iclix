<?php

use App\Models\Content;
use App\Models\Episode;
use App\Models\Season;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Storage;

/**
 * @return array{0: Content, 1: Season, 2: Episode, 3: Episode}
 */
function seriesWithTwoEpisodes(): array
{
    $series = Content::factory()->series()->create();
    $season = Season::factory()->for($series)->create(['season_number' => 1]);
    $first = Episode::factory()->for($season)->create(['episode_number' => 1, 'title' => 'Bagian Pertama']);
    $second = Episode::factory()->for($season)->create(['episode_number' => 2, 'title' => 'Bagian Kedua']);

    return [$series, $season, $first, $second];
}

test('the player resumes from the saved position and counts the view', function () {
    [, $profile] = subscribedMember();
    $movie = Content::factory()->create();
    WatchHistory::factory()->for($profile)->for($movie)->create(['position_sec' => 120, 'duration_sec' => 600]);

    $response = watchingAs($profile)->get(route('watch', $movie))->assertOk();

    expect($response->viewData('resumeFrom'))->toBe(120)
        ->and($movie->refresh()->views)->toBe(1);
});

test('a finished video starts over', function () {
    [, $profile] = subscribedMember();
    $movie = Content::factory()->create();
    WatchHistory::factory()->for($profile)->for($movie)->create(['position_sec' => 599, 'duration_sec' => 600]);

    expect(watchingAs($profile)->get(route('watch', $movie))->viewData('resumeFrom'))->toBe(0);
});

test('a series opens its first episode and offers the next one', function () {
    [, $profile] = subscribedMember();
    [$series, , $first, $second] = seriesWithTwoEpisodes();

    $response = watchingAs($profile)->get(route('watch', $series))->assertOk();

    expect($response->viewData('episode')->id)->toBe($first->id)
        ->and($response->viewData('nextEpisode')->id)->toBe($second->id);
});

test('a series resumes at the last watched episode', function () {
    [, $profile] = subscribedMember();
    [$series, , , $second] = seriesWithTwoEpisodes();
    WatchHistory::factory()->for($profile)->for($series)->create(['episode_id' => $second->id, 'position_sec' => 30]);

    $response = watchingAs($profile)->get(route('watch', $series))->assertOk();

    expect($response->viewData('episode')->id)->toBe($second->id)
        ->and($response->viewData('resumeFrom'))->toBe(30)
        ->and($response->viewData('nextEpisode'))->toBeNull();
});

test('a series without episodes sends the viewer back to its detail page', function () {
    [, $profile] = subscribedMember();
    $series = Content::factory()->series()->create();

    watchingAs($profile)->get(route('watch', $series))->assertRedirect(route('contents.show', $series));
});

test('an episode of another series cannot be opened through this series', function () {
    [, $profile] = subscribedMember();
    [$series] = seriesWithTwoEpisodes();
    [, , $foreignEpisode] = seriesWithTwoEpisodes();

    watchingAs($profile)->get(route('watch', [$series, $foreignEpisode]))->assertNotFound();
    watchingAs($profile)->get(route('stream', [$series, $foreignEpisode]))->assertNotFound();
});

test('the stream sends the video to subscribers', function () {
    Storage::fake('local');
    [, $profile] = subscribedMember();
    Storage::disk('local')->put('videos/movie.mp4', str_repeat('a', 100));
    $movie = Content::factory()->create(['video_path' => 'videos/movie.mp4']);

    $response = watchingAs($profile)->get(route('stream', $movie));

    $response->assertOk()->assertHeader('Accept-Ranges', 'bytes');
    expect($response->headers->get('Content-Type'))->toBe('video/mp4');
});

test('the stream answers range requests with partial content so seeking works', function () {
    Storage::fake('local');
    [, $profile] = subscribedMember();
    Storage::disk('local')->put('videos/movie.mp4', str_repeat('a', 100));
    $movie = Content::factory()->create(['video_path' => 'videos/movie.mp4']);

    watchingAs($profile)
        ->get(route('stream', $movie), ['Range' => 'bytes=10-19'])
        ->assertStatus(206)
        ->assertHeader('Content-Range', 'bytes 10-19/100')
        ->assertHeader('Content-Length', '10');
});

test('the stream answers not found when the file is missing', function () {
    Storage::fake('local');
    [, $profile] = subscribedMember();

    watchingAs($profile)
        ->get(route('stream', Content::factory()->create(['video_path' => 'videos/none.mp4'])))
        ->assertNotFound();
});

test('videos are not stored under the public web root', function () {
    expect(config('filesystems.disks.local.root'))->toBe(storage_path('app/private'));
});

test('progress is saved per profile and updated in place', function () {
    [, $profile] = subscribedMember();
    $movie = Content::factory()->create();
    $payload = ['content_id' => $movie->id, 'duration_sec' => 600];

    watchingAs($profile)->postJson(route('progress.store'), $payload + ['position_sec' => 10])->assertOk();
    watchingAs($profile)->postJson(route('progress.store'), $payload + ['position_sec' => 20])->assertOk();

    $history = $profile->watchHistories()->sole();

    expect($history->position_sec)->toBe(20)->and($history->duration_sec)->toBe(600);
});

test('episodes of a series keep separate progress', function () {
    [, $profile] = subscribedMember();
    [$series, , $first, $second] = seriesWithTwoEpisodes();

    foreach ([$first, $second] as $episode) {
        watchingAs($profile)->postJson(route('progress.store'), [
            'content_id' => $series->id,
            'episode_id' => $episode->id,
            'position_sec' => 5,
            'duration_sec' => 60,
        ])->assertOk();
    }

    expect($profile->watchHistories()->count())->toBe(2);
});

test('the position can never exceed the duration', function () {
    [, $profile] = subscribedMember();

    watchingAs($profile)->postJson(route('progress.store'), [
        'content_id' => Content::factory()->create()->id,
        'position_sec' => 999,
        'duration_sec' => 60,
    ])->assertOk();

    expect($profile->watchHistories()->sole()->position_sec)->toBe(60);
});

test('progress rejects an episode that does not belong to the content', function () {
    [, $profile] = subscribedMember();
    [$series] = seriesWithTwoEpisodes();
    [, , $foreignEpisode] = seriesWithTwoEpisodes();

    watchingAs($profile)->postJson(route('progress.store'), [
        'content_id' => $series->id,
        'episode_id' => $foreignEpisode->id,
        'position_sec' => 5,
        'duration_sec' => 60,
    ])->assertJsonValidationErrors('episode_id');
});

test('progress rejects a series without an episode', function () {
    [, $profile] = subscribedMember();
    [$series] = seriesWithTwoEpisodes();

    watchingAs($profile)->postJson(route('progress.store'), [
        'content_id' => $series->id,
        'position_sec' => 5,
        'duration_sec' => 60,
    ])->assertJsonValidationErrors('episode_id');
});
