<?php


namespace AppCompass\AppCompass\Events;

class Registering
{

    /**
     * The validated registration form data.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     *
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
}
