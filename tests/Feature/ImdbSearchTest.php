<?php

use App\Enums\ContentType;
use App\Models\Content;
use App\Support\ContentMedia;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
    config(['services.omdb.key' => 'test-key']);
});

/**
 * @return array<string, mixed>
 */
function omdbSearch(array ...$hits): array
{
    return ['Response' => 'True', 'Search' => $hits, 'totalResults' => (string) count($hits)];
}

/**
 * @return array<string, string>
 */
function omdbHit(string $imdbId, string $title, string $year = '2018'): array
{
    return ['imdbID' => $imdbId, 'Title' => $title, 'Year' => $year, 'Type' => 'movie', 'Poster' => IMDB_POSTER];
}

test('search lists catalog matches first, then IMDb films that are not in the catalog yet', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbSearch(
        omdbHit('tt1270797', 'Venom'),
        omdbHit('tt7097896', 'Venom: Let There Be Carnage', '2021'),
    ))]);
    [, $profile] = subscribedMember();
    Content::factory()->create(['title' => 'Venom', 'imdb_id' => 'tt1270797', 'views' => 10]);

    watchingAs($profile)->getJson(route('search', ['q' => 'venom']))
        ->assertOk()
        ->assertJsonCount(2, 'results')
        ->assertJsonPath('results.0.title', 'Venom')
        ->assertJsonPath('results.0.id', Content::query()->sole()->id)
        ->assertJsonPath('results.1.title', 'Venom: Let There Be Carnage')
        ->assertJsonPath('results.1.year', 2021)
        ->assertJsonPath('results.1.poster', IMDB_POSTER_RESIZED)
        ->assertJsonPath('results.1.url', route('imdb.import', 'tt7097896'));

    Http::assertSent(fn (Request $request) => $request['s'] === 'venom' && $request['type'] === 'movie');
});

test('a catalog title is found through IMDb even when its stored title differs', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbSearch(omdbHit('tt0910970', 'WALL·E', '2008')))]);
    [, $profile] = subscribedMember();
    $content = Content::factory()->create(['title' => 'Robot Kecil', 'imdb_id' => 'tt0910970']);

    watchingAs($profile)->getJson(route('search', ['q' => 'wall']))
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.id', $content->id);
});

test('repeating a search reuses the cached IMDb answer', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbSearch(omdbHit('tt1270797', 'Venom')))]);
    [, $profile] = subscribedMember();

    watchingAs($profile)->getJson(route('search', ['q' => 'Venom']))->assertJsonCount(1, 'results');
    watchingAs($profile)->getJson(route('search', ['q' => 'venom ']))->assertJsonCount(1, 'results');

    Http::assertSentCount(1);
});

test('search still answers from the catalog when IMDb fails', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(['Response' => 'False', 'Error' => 'Request limit reached!'])]);
    [, $profile] = subscribedMember();
    Content::factory()->create(['title' => 'Venom']);

    watchingAs($profile)->getJson(route('search', ['q' => 'venom']))
        ->assertOk()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.title', 'Venom');
});

test('IMDb is not searched for terms shorter than three characters or for kids profiles', function (bool $isKidsProfile, string $term) {
    [, $profile] = subscribedMember(isKidsProfile: $isKidsProfile);

    watchingAs($profile)->getJson(route('search', ['q' => $term]))->assertOk()->assertJsonCount(0, 'results');

    Http::assertNothingSent();
})->with([
    'short term' => [false, 've'],
    'kids profile' => [true, 'venom'],
]);

test('opening an IMDb film adds it to the catalog once, with the sample video', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbTitle())]);
    [, $profile] = subscribedMember();

    watchingAs($profile)->get(route('imdb.import', 'tt0816692'))
        ->assertRedirect(route('contents.show', 'interstellar'));

    expect(Content::query()->sole())
        ->type->toBe(ContentType::Movie)
        ->title->toBe('Interstellar')
        ->synopsis->toBe('A team of explorers travel through a wormhole in space.')
        ->imdb_rating->toBe(8.7)
        ->duration_min->toBe(169)
        ->video_path->toBe(ContentMedia::SAMPLE_VIDEO_PATH);

    watchingAs($profile)->get(route('imdb.import', 'tt0816692'))
        ->assertRedirect(route('contents.show', 'interstellar'));

    expect(Content::query()->count())->toBe(1);
    Http::assertSentCount(1);
});

test('an imported film whose title is taken gets the year in its slug', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbTitle())]);
    [, $profile] = subscribedMember();
    Content::factory()->create(['slug' => 'interstellar']);

    watchingAs($profile)->get(route('imdb.import', 'tt0816692'))
        ->assertRedirect(route('contents.show', 'interstellar-2014'));
});

test('opening an IMDb series or an unknown title returns to search with an error', function (array $payload) {
    Http::fake([OMDB_ENDPOINT => Http::response($payload)]);
    [, $profile] = subscribedMember();

    watchingAs($profile)->get(route('imdb.import', 'tt0903747'))
        ->assertRedirect(route('search'))
        ->assertSessionHas('error');

    expect(Content::query()->count())->toBe(0);
})->with([
    'series' => [omdbTitle(['imdbID' => 'tt0903747', 'Type' => 'series'])],
    'unknown' => [['Response' => 'False', 'Error' => 'Incorrect IMDb ID.']],
]);

test('a kids profile cannot open an imported film that is not rated for kids', function () {
    Http::fake([OMDB_ENDPOINT => Http::response(omdbTitle(['Rated' => 'R']))]);
    [, $kidsProfile] = subscribedMember(isKidsProfile: true);

    watchingAs($kidsProfile)->get(route('imdb.import', 'tt0816692'))->assertNotFound();
});

test('the import route only accepts IMDb IDs', function () {
    [, $profile] = subscribedMember();

    watchingAs($profile)->get('/title/imdb/venom')->assertNotFound();

    Http::assertNothingSent();
});
