<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Show the simulated checkout confirmation.
     */
    public function show(Plan $plan): View
    {
        return view('plans.checkout', ['plan' => $plan]);
    }

    /**
     * Activate the plan. Payment is simulated, so confirming starts the subscription directly.
     */
    public function store(Request $request, Plan $plan): RedirectResponse
    {
        DB::transaction(function () use ($request, $plan): void {
            $user = $request->user();

            $user->subscriptions()
                ->where('status', SubscriptionStatus::Active)
                ->update(['status' => SubscriptionStatus::Expired]);

            $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
                'status' => SubscriptionStatus::Active,
                'amount' => $plan->price,
                'invoice_code' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
            ]);
        });

        return redirect()->route('profiles.index')
            ->with('status', "Langganan {$plan->name} aktif selama {$plan->duration_days} hari.");
    }
}
