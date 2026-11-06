<?php

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

test('the plans page lists every plan', function () {
    $plans = Plan::factory()->count(3)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('plans.index'))
        ->assertOk()
        ->assertSeeInOrder($plans->sortBy('price')->pluck('name')->all());
});

test('checkout shows the plan price', function () {
    $plan = Plan::factory()->create(['price' => 69000]);

    $this->actingAs(User::factory()->create())
        ->get(route('checkout.show', $plan))
        ->assertOk()
        ->assertSee('Rp69.000');
});

test('confirming checkout activates the plan for its duration', function () {
    $this->freezeSecond();
    $user = User::factory()->create();
    $plan = Plan::factory()->create(['price' => 69000, 'duration_days' => 30]);

    $this->actingAs($user)
        ->post(route('checkout.store', $plan))
        ->assertRedirect(route('profiles.index'));

    $subscription = $user->subscriptions()->sole();

    expect($subscription->plan_id)->toBe($plan->id)
        ->and($subscription->amount)->toBe(69000)
        ->and($subscription->status)->toBe(SubscriptionStatus::Active)
        ->and($subscription->ends_at->equalTo(now()->addDays(30)))->toBeTrue()
        ->and($subscription->invoice_code)->toStartWith('INV-')
        ->and($user->hasActiveSubscription())->toBeTrue();
});

test('changing plan closes the previous subscription', function () {
    $user = User::factory()->create();
    $previous = Subscription::factory()->for($user)->create();
    $newPlan = Plan::factory()->create();

    $this->actingAs($user)->post(route('checkout.store', $newPlan));

    expect($previous->refresh()->status)->toBe(SubscriptionStatus::Expired)
        ->and($user->activeSubscription()->first()->plan_id)->toBe($newPlan->id);
});

test('the account page shows the transaction history and end date', function () {
    $user = User::factory()->create();
    $subscription = Subscription::factory()->for($user)->create(['ends_at' => '2030-03-09 10:00:00']);

    $this->actingAs($user)
        ->get(route('account.edit'))
        ->assertOk()
        ->assertSee($subscription->invoice_code)
        ->assertSee('9 Maret 2030');
});
