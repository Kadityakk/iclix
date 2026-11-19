<?php

use App\Http\Middleware\EnsureProfileIsSelected;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Create a member with an active subscription and one profile.
 *
 * @return array{0: User, 1: Profile}
 */
function subscribedMember(bool $isKidsProfile = false): array
{
    $user = User::factory()->create();
    Subscription::factory()->for($user)->create();
    $profile = Profile::factory()->for($user)->create(['is_kids' => $isKidsProfile]);

    return [$user, $profile];
}

/**
 * Authenticate as the profile's owner with that profile selected.
 */
function watchingAs(Profile $profile): TestCase
{
    return test()
        ->actingAs($profile->user)
        ->withSession([EnsureProfileIsSelected::SESSION_KEY => $profile->id]);
}

const OMDB_ENDPOINT = 'www.omdbapi.com/*';

const IMDB_POSTER = 'https://m.media-amazon.com/images/M/MV5BYzdjMDAx@._V1_SX300.jpg';

const IMDB_POSTER_RESIZED = 'https://m.media-amazon.com/images/M/MV5BYzdjMDAx@._V1_QL75_UX500_.jpg';

/**
 * An OMDb title payload for Interstellar.
 *
 * @return array<string, string>
 */
function omdbTitle(array $overrides = []): array
{
    return $overrides + [
        'Response' => 'True',
        'imdbID' => 'tt0816692',
        'Title' => 'Interstellar',
        'Type' => 'movie',
        'Year' => '2014',
        'Rated' => 'PG-13',
        'Genre' => 'Adventure, Drama, Sci-Fi',
        'Plot' => 'A team of explorers travel through a wormhole in space.',
        'Released' => '07 Nov 2014',
        'Runtime' => '169 min',
        'Actors' => 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
        'Poster' => IMDB_POSTER,
        'imdbRating' => '8.7',
    ];
}
