<?php

namespace P3in\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use P3in\Events\Login;
use P3in\Events\Logout;

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

        $permissions = $user->allPermissions();

        Cache::tags('auth_permissions')->forever($user->id, $permissions);
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event)
    {
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
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
    }
}
