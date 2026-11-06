<?php

use App\Models\Content;
use App\Models\Genre;
use App\Models\WatchHistory;

test('the home page shows featured, trending and genre rows', function () {
    [, $profile] = subscribedMember();
    $genre = Genre::factory()->create(['name' => 'Fantasi Kosmik']);
    $featured = Content::factory()->featured()->create(['title' => 'Judul Unggulan']);
    $inGenre = Content::factory()->create(['title' => 'Judul Bergenre']);
    $inGenre->genres()->attach($genre);

    watchingAs($profile)
        ->get(route('browse'))
        ->assertOk()
        ->assertSee($featured->title)
        ->assertSee('Trending')
        ->assertSee('Fantasi Kosmik')
        ->assertSee('Judul Bergenre');
});

test('the hero never carries more than five featured titles', function () {
    [, $profile] = subscribedMember();
    Content::factory()->featured()->count(7)->create();

    $response = watchingAs($profile)->get(route('browse'))->assertOk();

    expect($response->viewData('featured'))->toHaveCount(Content::MAX_FEATURED);
});

test('continue watching shows unfinished titles and skips finished ones', function () {
    [, $profile] = subscribedMember();
    $unfinished = WatchHistory::factory()->for($profile)->create(['position_sec' => 100, 'duration_sec' => 600]);
    $finished = WatchHistory::factory()->for($profile)->create(['position_sec' => 590, 'duration_sec' => 600]);

    $response = watchingAs($profile)->get(route('browse'))->assertOk();

    expect($response->viewData('continueWatching')->pluck('id')->all())->toBe([$unfinished->id])
        ->and($finished->isFinished())->toBeTrue();
});

test('watch history of another profile stays private', function () {
    [, $profile] = subscribedMember();
    WatchHistory::factory()->create();

    $response = watchingAs($profile)->get(route('browse'))->assertOk();

    expect($response->viewData('continueWatching'))->toBeEmpty();
});

test('movies and series are listed on separate pages', function () {
    [, $profile] = subscribedMember();
    $movie = Content::factory()->create(['title' => 'Sebuah Film']);
    $series = Content::factory()->series()->create(['title' => 'Sebuah Serial']);

    watchingAs($profile)->get(route('movies.index'))->assertOk()->assertSee($movie->title)->assertDontSee($series->title);
    watchingAs($profile)->get(route('series.index'))->assertOk()->assertSee($series->title)->assertDontSee($movie->title);
});

test('the catalog filters by genre and year', function () {
    [, $profile] = subscribedMember();
    $genre = Genre::factory()->create();
    $match = Content::factory()->create(['title' => 'Cocok Sekali', 'release_year' => 2020]);
    $match->genres()->attach($genre);
    $otherYear = Content::factory()->create(['title' => 'Beda Tahun', 'release_year' => 2021]);
    $otherYear->genres()->attach($genre);
    Content::factory()->create(['title' => 'Beda Genre', 'release_year' => 2020]);

    watchingAs($profile)
        ->get(route('movies.index', ['genre' => $genre->slug, 'year' => 2020]))
        ->assertOk()
        ->assertSee('Cocok Sekali')
        ->assertDontSee('Beda Tahun')
        ->assertDontSee('Beda Genre');
});

test('the detail page shows synopsis, cast and genres', function () {
    [, $profile] = subscribedMember();
    $genre = Genre::factory()->create(['name' => 'Misteri Laut']);
    $content = Content::factory()->create(['synopsis' => 'Sinopsis khusus', 'cast_members' => ['Pemeran Satu']]);
    $content->genres()->attach($genre);

    watchingAs($profile)
        ->get(route('contents.show', $content))
        ->assertOk()
        ->assertSee('Sinopsis khusus')
        ->assertSee('Pemeran Satu')
        ->assertSee('Misteri Laut');
});

test('similar titles share a genre and exclude the title itself', function () {
    [, $profile] = subscribedMember();
    $genre = Genre::factory()->create();
    $content = Content::factory()->create();
    $content->genres()->attach($genre);
    $similar = Content::factory()->create();
    $similar->genres()->attach($genre);
    Content::factory()->create();

    $response = watchingAs($profile)->get(route('contents.show', $content))->assertOk();

    expect($response->viewData('similar')->modelKeys())->toBe([$similar->id]);
});

test('search answers json with matching titles, ignoring case', function () {
    [, $profile] = subscribedMember();
    Content::factory()->create(['title' => 'Malam Panjang']);
    Content::factory()->create(['title' => 'Pagi Cerah']);

    watchingAs($profile)
        ->getJson(route('search', ['q' => 'malam']))
        ->assertOk()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.title', 'Malam Panjang');
});

test('search treats wildcard characters literally', function (string $term) {
    [, $profile] = subscribedMember();
    Content::factory()->create(['title' => 'Malam Panjang']);

    watchingAs($profile)->getJson(route('search', ['q' => $term]))->assertJsonCount(0, 'results');
})->with(['%', '_', '!']);

test('an empty search returns nothing', function () {
    [, $profile] = subscribedMember();
    Content::factory()->create();

    watchingAs($profile)->getJson(route('search'))->assertOk()->assertJsonCount(0, 'results');
});

test('kids profiles only see titles rated SU or 7+', function () {
    [, $kidsProfile] = subscribedMember(isKidsProfile: true);
    Content::factory()->forKids()->create(['title' => 'Kartun Ceria']);
    $adultContent = Content::factory()->forAdults()->create(['title' => 'Malam Berdarah']);

    watchingAs($kidsProfile)->get(route('movies.index'))->assertSee('Kartun Ceria')->assertDontSee('Malam Berdarah');
    watchingAs($kidsProfile)->getJson(route('search', ['q' => 'Malam']))->assertJsonCount(0, 'results');
    watchingAs($kidsProfile)->get(route('contents.show', $adultContent))->assertNotFound();
    watchingAs($kidsProfile)->get(route('watch', $adultContent))->assertNotFound();
    watchingAs($kidsProfile)->get(route('stream', $adultContent))->assertNotFound();
});
