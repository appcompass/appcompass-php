<?php

namespace AppCompass\Events;

use Illuminate\Queue\SerializesModels;

class UserCurrentCompanySet
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