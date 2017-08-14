<?php

namespace P3in\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Notification;
use P3in\Listeners\UserEventSubscriber;
use P3in\Models\Role;
use P3in\Notifications\QueueAfter;
use P3in\Notifications\QueueBefore;
use P3in\Notifications\QueueFailing;

class AppCompassEventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        // @TODO: hook in our notification system to update cp users on what's going on.
        // Also prob a good idea to add additional notification channels as need be.
        // i.e. what ever channels are defined for this pilot-io install.
        // Queue::failing(function (JobFailed $event) {
        //     Notification::send(Role::whereName('admin')->first()->users, new QueueFailing($event));
        // });

        // Queue::before(function (JobProcessing $event) {
        //     Notification::send(Role::whereName('admin')->first()->users, new QueueBefore($event));
        // });

        // Queue::after(function (JobProcessed $event) {
        //     Notification::send(Role::whereName('admin')->first()->users, new QueueAfter($event));
        // });
        //
    }
}
