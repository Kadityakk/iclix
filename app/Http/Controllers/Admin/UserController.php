<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List users with a name or email search.
     */
    public function index(Request $request): View
    {
        $term = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;

        return view('admin.users.index', [
            'users' => User::query()
                ->when($term, function (Builder $query, string $term): void {
                    $query->where(fn (Builder $query) => $query
                        ->whereLike('name', "%{$term}%")
                        ->orWhereLike('email', "%{$term}%"));
                })
                ->with('activeSubscription.plan')
                ->withCount('profiles')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    /**
     * Show one user with their profiles and transactions.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->load('profiles'),
            'subscriptions' => $user->subscriptions()->with('plan')->latest('starts_at')->latest('id')->get(),
        ]);
    }

    /**
     * Activate or deactivate an account. Admins cannot lock themselves out.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate(['is_active' => ['required', 'boolean']]);

        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->forceFill(['is_active' => $validated['is_active']])->save();

        return back()->with('status', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }
}
