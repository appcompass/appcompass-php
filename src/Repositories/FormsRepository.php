<?php

namespace P3in\Repositories;

use P3in\Models\Form;
use P3in\Repositories\Eloquent\Repository;

class FormsRepository extends Repository
{
    public function getModel()
    {
        return Form::class;
    }
}
