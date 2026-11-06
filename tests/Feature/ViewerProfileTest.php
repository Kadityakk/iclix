<?php

use App\Http\Middleware\EnsureProfileIsSelected;
use App\Models\Profile;
use App\Models\User;

test('registering creates a first profile', function () {
    $this->post('/register', [
        'name' => 'Raka',
        'email' => 'raka@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect(User::query()->sole()->profiles()->sole()->name)->toBe('Raka');
});

test('a profile can be created', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('profiles.store'), ['name' => 'Adik', 'avatar' => 'rose', 'is_kids' => '1'])
        ->assertRedirect(route('profiles.index'));

    $profile = $user->profiles()->sole();

    expect($profile->name)->toBe('Adik')
        ->and($profile->avatar)->toBe('rose')
        ->and($profile->is_kids)->toBeTrue();
});

test('an account cannot have more than four profiles', function () {
    $user = User::factory()->create();
    Profile::factory()->for($user)->count(4)->create();

    $this->actingAs($user)
        ->post(route('profiles.store'), ['name' => 'Kelima', 'avatar' => 'rose'])
        ->assertSessionHasErrors(['name' => 'Satu akun maksimal punya 4 profil.']);

    expect($user->profiles()->count())->toBe(4);
});

test('an unknown avatar is rejected', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('profiles.store'), ['name' => 'Adik', 'avatar' => 'not-an-avatar'])
        ->assertSessionHasErrors('avatar');
});

test('another account\'s profile is answered with not found', function (string $method, string $routeName) {
    $foreignProfile = Profile::factory()->create();

    $this->actingAs(User::factory()->create())
        ->call($method, route($routeName, $foreignProfile), ['name' => 'X', 'avatar' => 'rose'])
        ->assertNotFound();

    expect($foreignProfile->fresh())->not->toBeNull()
        ->and($foreignProfile->fresh()->name)->toBe($foreignProfile->name);
})->with([
    ['GET', 'profiles.edit'],
    ['PUT', 'profiles.update'],
    ['DELETE', 'profiles.destroy'],
    ['POST', 'profiles.select'],
]);

test('selecting a profile stores it and opens the home page', function () {
    $profile = Profile::factory()->create();

    $this->actingAs($profile->user)
        ->post(route('profiles.select', $profile))
        ->assertRedirect(route('browse'))
        ->assertSessionHas(EnsureProfileIsSelected::SESSION_KEY, $profile->id);
});

test('deleting the selected profile forgets the selection', function () {
    [, $profile] = subscribedMember();

    watchingAs($profile)
        ->delete(route('profiles.destroy', $profile))
        ->assertSessionMissing(EnsureProfileIsSelected::SESSION_KEY);

    expect($profile->fresh())->toBeNull();
});
