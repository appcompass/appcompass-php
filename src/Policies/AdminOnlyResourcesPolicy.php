<?php

namespace AppCompass\AppCompass\Policies;

use AppCompass\AppCompass\Models\User;

class AdminOnlyResourcesPolicy
{

    /**
     *  Check for root
     */
    public function before($user, $perm)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function index(User $user)
    {
        return false;
    }

    public function show(User $user)
    {
        return false;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user)
    {
        return false;
    }

    public function destroy(User $user)
    {
        return false;
    }
}
