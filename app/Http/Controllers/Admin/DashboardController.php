<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the summary statistics.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'memberCount' => User::query()->where('role', UserRole::Member)->count(),
            'activeSubscriptionCount' => Subscription::query()
                ->where('status', SubscriptionStatus::Active)
                ->where('ends_at', '>', now())
                ->count(),
            'contentCount' => Content::query()->count(),
            'topContents' => Content::query()->orderByDesc('views')->limit(5)->get(),
        ]);
    }
}
