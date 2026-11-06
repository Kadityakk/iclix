<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProfilePolicy
{
    /**
     * Profiles of other accounts are answered with a 404 so their existence stays hidden.
     */
    public function manage(User $user, Profile $profile): Response
    {
        return $profile->user()->is($user) ? Response::allow() : Response::denyAsNotFound();
    }
}
