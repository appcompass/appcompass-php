<?php

namespace P3in\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use P3in\Events\Login;
use P3in\Events\Logout;
use P3in\Events\UserCheck;

class UserEventSubscriber
{

    /**
     * Handle user login events.
     */
    public function onUserLogin($event)
    {
        $user = $event->user;
        // lets save the last_login timestamp.
        $user->last_login = Carbon::now();
        $user->save();

        $this->setPermissions($user);
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event)
    {
        $user = $event->user;

        Cache::tags('auth_permissions')->forget($user->id);
    }

    /**
     * Handle user check events.
     */
    public function onUserCheck($event)
    {
        $this->setPermissions($event->user);
    }

    private function setPermissions($user)
    {
        $permissions = $user->allPermissions();

        Cache::tags('auth_permissions')->forever($user->id, $permissions);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            Login::class,
            'P3in\Listeners\UserEventSubscriber@onUserLogin'
        );

        $events->listen(
            Logout::class,
            'P3in\Listeners\UserEventSubscriber@onUserLogout'
        );

        $events->listen(
            UserCheck::class,
            'P3in\Listeners\UserEventSubscriber@onUserCheck'
        );
    }
}
