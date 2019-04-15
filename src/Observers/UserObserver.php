<?php

namespace AppCompass\AppCompass\Observers;

use Illuminate\Database\Eloquent\Model;

class UserObserver
{
    public function created(Model $model)
    {
        $model->assignRole('user');
    }
}
