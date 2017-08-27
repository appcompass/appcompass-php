<?php


namespace P3in\Events;

use Illuminate\Queue\SerializesModels;

class Registered
{

    use SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * The validated registration form data.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array                                      $data
     *
     */
    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
