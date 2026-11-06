<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Plan;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Show the public landing page.
     */
    public function __invoke(): View
    {
        return view('landing', [
            'plans' => Plan::query()->orderBy('price')->get(),
            'posters' => Content::query()
                ->whereNotNull('poster')
                ->orderByDesc('views')
                ->limit(6)
                ->get(),
        ]);
    }
}
