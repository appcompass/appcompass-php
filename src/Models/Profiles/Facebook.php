<?php

namespace P3in\Models\Profiles;

use Illuminate\Database\Eloquent\Model;
use P3in\Traits\IsProfileTrait;

class Facebook extends Model
{
    use IsProfileTrait;

    public function __construct($data)
    {
        $this->attributes = $data;
    }
}
