<?php

use App\Models\Content;
use App\Models\Rating;

test('a title can be saved and removed without a reload', function () {
    [, $profile] = subscribedMember();
    $content = Content::factory()->create();

    watchingAs($profile)->postJson(route('my-list.store', $content))->assertOk()->assertJson(['saved' => true]);
    expect($profile->watchlistContents()->modelKeys())->toBe([$content->id]);

    watchingAs($profile)->deleteJson(route('my-list.destroy', $content))->assertOk()->assertJson(['saved' => false]);
    expect($profile->watchlistContents()->count())->toBe(0);
});

test('saving the same title twice keeps one entry', function () {
    [, $profile] = subscribedMember();
    $content = Content::factory()->create();

    watchingAs($profile)->postJson(route('my-list.store', $content))->assertOk();
    watchingAs($profile)->postJson(route('my-list.store', $content))->assertOk();

    expect($profile->watchlistContents()->count())->toBe(1);
});

test('my list only contains the current profile\'s titles', function () {
    [$user, $profile] = subscribedMember();
    $otherProfile = $user->profiles()->create(['name' => 'Lain']);
    $mine = Content::factory()->create(['title' => 'Punya Saya']);
    $theirs = Content::factory()->create(['title' => 'Punya Dia']);
    $profile->watchlistContents()->attach($mine);
    $otherProfile->watchlistContents()->attach($theirs);

    watchingAs($profile)->get(route('my-list.index'))->assertOk()->assertSee('Punya Saya')->assertDontSee('Punya Dia');
});

test('kids profiles cannot save restricted titles', function () {
    [, $kidsProfile] = subscribedMember(isKidsProfile: true);

    watchingAs($kidsProfile)->postJson(route('my-list.store', Content::factory()->forAdults()->create()))->assertNotFound();

    expect($kidsProfile->watchlistContents()->count())->toBe(0);
});

test('a rating can be set, changed and cleared', function () {
    [, $profile] = subscribedMember();
    $content = Content::factory()->create();

    watchingAs($profile)->postJson(route('ratings.store', $content), ['value' => Rating::LIKE])->assertOk();
    expect($profile->ratings()->sole()->value)->toBe(Rating::LIKE);

    watchingAs($profile)->postJson(route('ratings.store', $content), ['value' => Rating::DISLIKE])->assertOk();
    expect($profile->ratings()->sole()->value)->toBe(Rating::DISLIKE);

    watchingAs($profile)->postJson(route('ratings.store', $content), ['value' => 0])->assertOk();
    expect($profile->ratings()->count())->toBe(0);
});

test('an invalid rating value is rejected', function () {
    [, $profile] = subscribedMember();

    watchingAs($profile)
        ->postJson(route('ratings.store', Content::factory()->create()), ['value' => 5])
        ->assertJsonValidationErrors('value');
});
