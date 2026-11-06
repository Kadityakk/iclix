<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * List the plans.
     */
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::query()->withCount('subscriptions')->orderBy('price')->get(),
        ]);
    }

    /**
     * Show the form for creating a plan.
     */
    public function create(): View
    {
        return view('admin.plans.form', ['plan' => new Plan(['duration_days' => 30])]);
    }

    /**
     * Store a new plan.
     */
    public function store(PlanRequest $request): RedirectResponse
    {
        Plan::create($request->validated());

        return redirect()->route('admin.plans.index')->with('status', 'Paket ditambahkan.');
    }

    /**
     * Show the form for editing a plan.
     */
    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', ['plan' => $plan]);
    }

    /**
     * Update a plan.
     */
    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($request->validated());

        return redirect()->route('admin.plans.index')->with('status', 'Paket diperbarui.');
    }

    /**
     * Delete a plan unless transactions still refer to it.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Paket yang sudah punya transaksi tidak bisa dihapus.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', 'Paket dihapus.');
    }
}
