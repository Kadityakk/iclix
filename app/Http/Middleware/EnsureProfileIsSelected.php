<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsSelected
{
    public const string SESSION_KEY = 'selected_profile_id';

    /**
     * Bind the session's profile into the container so controllers can type-hint it.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->profiles()->find($request->session()->get(self::SESSION_KEY));

        if ($profile === null) {
            if ($request->expectsJson()) {
                abort(Response::HTTP_FORBIDDEN, 'Pilih profil terlebih dahulu.');
            }

            return redirect()->guest(route('profiles.index'));
        }

        app()->instance(Profile::class, $profile);

        return $next($request);
    }
}
