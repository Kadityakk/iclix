<?php

use App\Models\Genre;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

test('a genre can be created, renamed and deleted', function () {
    $this->post(route('admin.genres.store'), ['name' => 'Fiksi Ilmiah'])->assertRedirect(route('admin.genres.index'));

    $genre = Genre::query()->sole();
    expect($genre->slug)->toBe('fiksi-ilmiah');

    $this->put(route('admin.genres.update', $genre), ['name' => 'Fiksi'])->assertSessionHasNoErrors();
    expect($genre->refresh()->slug)->toBe('fiksi');

    $this->delete(route('admin.genres.destroy', $genre));
    expect(Genre::query()->count())->toBe(0);
});

test('genre names are unique', function () {
    Genre::factory()->create(['name' => 'Drama', 'slug' => 'drama']);

    $this->post(route('admin.genres.store'), ['name' => 'Drama'])->assertSessionHasErrors('name');
});

test('a plan can be created and updated', function () {
    $payload = ['name' => 'Hemat', 'price' => 25000, 'quality' => '720p', 'max_screens' => 1, 'duration_days' => 30];

    $this->post(route('admin.plans.store'), $payload)->assertRedirect(route('admin.plans.index'));

    $plan = Plan::query()->sole();
    $this->put(route('admin.plans.update', $plan), ['price' => 30000] + $payload)->assertSessionHasNoErrors();

    expect($plan->refresh()->price)->toBe(30000);
});

test('a plan with an unknown quality is rejected', function () {
    $this->post(route('admin.plans.store'), ['name' => 'X', 'price' => 1, 'quality' => '8K', 'max_screens' => 1, 'duration_days' => 30])
        ->assertSessionHasErrors('quality');
});

test('a plan with transactions cannot be deleted', function () {
    $plan = Subscription::factory()->create()->plan;

    $this->from(route('admin.plans.index'))
        ->delete(route('admin.plans.destroy', $plan))
        ->assertSessionHas('error');

    expect($plan->fresh())->not->toBeNull();
});

test('a plan without transactions can be deleted', function () {
    $plan = Plan::factory()->create();

    $this->delete(route('admin.plans.destroy', $plan));

    expect($plan->fresh())->toBeNull();
});

test('users can be searched by name or email', function () {
    User::factory()->create(['name' => 'Sinta Dewi', 'email' => 'sinta@example.com']);
    User::factory()->create(['name' => 'Budi', 'email' => 'budi@example.com']);

    $this->get(route('admin.users.index', ['q' => 'sinta']))->assertOk()->assertSee('Sinta Dewi')->assertDontSee('budi@example.com');
    $this->get(route('admin.users.index', ['q' => 'budi@']))->assertOk()->assertSee('budi@example.com')->assertDontSee('Sinta Dewi');
});

test('a user detail page shows their transactions', function () {
    $subscription = Subscription::factory()->create();

    $this->get(route('admin.users.show', $subscription->user))->assertOk()->assertSee($subscription->invoice_code);
});

test('an account can be deactivated and reactivated', function () {
    $user = User::factory()->create();

    $this->patch(route('admin.users.update', $user), ['is_active' => 0]);
    expect($user->refresh()->is_active)->toBeFalse();

    $this->patch(route('admin.users.update', $user), ['is_active' => 1]);
    expect($user->refresh()->is_active)->toBeTrue();
});

test('admins cannot deactivate their own account', function () {
    $this->patch(route('admin.users.update', $this->admin), ['is_active' => 0])->assertSessionHas('error');

    expect($this->admin->refresh()->is_active)->toBeTrue();
});

test('registration cannot grant the admin role', function () {
    auth()->logout();

    $this->post('/register', [
        'name' => 'Penyusup',
        'email' => 'penyusup@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    expect(User::query()->where('email', 'penyusup@example.com')->sole()->isAdmin())->toBeFalse();
});
