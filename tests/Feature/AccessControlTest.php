<?php

use App\Models\Content;
use App\Models\Profile;
use App\Models\Subscription;
use App\Models\User;

test('guests are sent to login from member and admin pages', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with(['/browse', '/plans', '/profiles', '/account', '/admin']);

test('the landing page redirects signed-in users to their area', function () {
    $this->actingAs(User::factory()->create())->get('/')->assertRedirect(route('dashboard'));
});

test('members without an active subscription are sent to the plans page', function (string $routeName) {
    $user = User::factory()->create();
    $content = Content::factory()->create();

    $url = match ($routeName) {
        'watch' => route('watch', $content),
        'stream' => route('stream', $content),
        default => route($routeName),
    };

    $this->actingAs($user)->get($url)->assertRedirect(route('plans.index'));
})->with(['browse', 'watch', 'stream', 'search']);

test('an expired subscription does not grant access', function () {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->expired()->create();

    $this->actingAs($user)->get(route('browse'))->assertRedirect(route('plans.index'));
});

test('a subscription past its end date does not grant access even if still marked active', function () {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->create(['ends_at' => now()->subMinute()]);

    $this->actingAs($user)->get(route('browse'))->assertRedirect(route('plans.index'));
});

test('json requests without a subscription get a 403 instead of a redirect', function () {
    $content = Content::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson(route('my-list.store', $content))
        ->assertForbidden();
});

test('subscribers must choose a profile before browsing', function () {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->create();

    $this->actingAs($user)->get(route('browse'))->assertRedirect(route('profiles.index'));
});

test('a profile of another account in the session is not accepted', function () {
    $user = User::factory()->create();
    Subscription::factory()->for($user)->create();
    $foreignProfile = Profile::factory()->create();

    $this->actingAs($user)
        ->withSession(['selected_profile_id' => $foreignProfile->id])
        ->get(route('browse'))
        ->assertRedirect(route('profiles.index'));
});

test('members cannot open the admin dashboard', function () {
    $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))->assertForbidden();
});

test('users land in the area for their role after login', function () {
    $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertRedirect(route('browse'));
});

test('deactivated accounts cannot log in', function () {
    $user = User::factory()->inactive()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => 'Akun ini telah dinonaktifkan. Hubungi admin iClix.']);

    $this->assertGuest();
});

test('deactivating an account ends its running session', function () {
    $user = User::factory()->inactive()->create();

    $this->actingAs($user)->get(route('plans.index'))->assertRedirect(route('login'));

    $this->assertGuest();
});
