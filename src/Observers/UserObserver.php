<?php

namespace P3in\Observers;

use Illuminate\Database\Eloquent\Model;

class UserObserver
{
    public function created(Model $model)
    {
        $model->assignRole('user');
    }
}
