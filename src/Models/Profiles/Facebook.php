<?php

namespace AppCompass\AppCompass\Models\Profiles;

use Illuminate\Database\Eloquent\Model;
use AppCompass\AppCompass\Traits\IsProfileTrait;

class Facebook extends Model
{
    use IsProfileTrait;

    public function __construct($data)
    {
        $this->attributes = $data;
    }
}
