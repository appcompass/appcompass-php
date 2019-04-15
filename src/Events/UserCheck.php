<?php

namespace AppCompass\AppCompass\Events;

use Illuminate\Queue\SerializesModels;

class UserCheck
{
    use SerializesModels;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
