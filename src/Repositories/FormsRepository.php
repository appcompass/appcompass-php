<?php

namespace AppCompass\Repositories;

use AppCompass\Models\Form;
use AppCompass\Repositories\Eloquent\Repository;

class FormsRepository extends Repository
{
    public function getModel()
    {
        return Form::class;
    }
}
