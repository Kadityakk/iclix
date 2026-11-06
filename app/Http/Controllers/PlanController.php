<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Show the subscription plans.
     */
    public function index(Request $request): View
    {
        return view('plans.index', [
            'plans' => Plan::query()->orderBy('price')->get(),
            'activeSubscription' => $request->user()->activeSubscription()->with('plan')->first(),
        ]);
    }
}
