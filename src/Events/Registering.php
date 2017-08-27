<?php


namespace P3in\Events;

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
