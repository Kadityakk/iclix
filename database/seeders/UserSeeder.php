<?php

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Content;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed the demo admin and a subscribed member with some viewing history.
     */
    public function run(): void
    {
        $this->seedAccount('Admin iClix', 'admin@iclix.test', UserRole::Admin);

        $member = $this->seedAccount('Raka Pratama', 'member@iclix.test', UserRole::Member);

        if ($member->subscriptions()->exists()) {
            return;
        }

        $plan = Plan::query()->where('name', 'Standar')->firstOrFail();

        $member->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
            'status' => SubscriptionStatus::Active,
            'amount' => $plan->price,
            'invoice_code' => 'INV-DEMO-'.Str::upper(Str::random(8)),
        ]);

        $profile = $member->profiles()->create(['name' => 'Raka', 'avatar' => 'violet']);
        $member->profiles()->create(['name' => 'Adik', 'avatar' => 'amber', 'is_kids' => true]);

        $movies = Content::query()->where('type', 'movie')->orderByDesc('views')->limit(4)->get();

        $profile->watchlistContents()->syncWithoutDetaching($movies->take(2)->modelKeys());

        foreach ($movies->skip(2) as $index => $movie) {
            $profile->watchHistories()->create([
                'content_id' => $movie->id,
                'position_sec' => 10 + $index * 5,
                'duration_sec' => 45,
            ]);
        }
    }

    private function seedAccount(string $name, string $email, UserRole $role): User
    {
        $user = User::query()->firstOrCreate(['email' => $email], ['name' => $name, 'password' => 'password']);

        $user->forceFill(['role' => $role, 'email_verified_at' => now()])->save();

        return $user;
    }
}
