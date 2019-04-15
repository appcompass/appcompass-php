<?php

namespace AppCompass\AppCompass\Repositories;

use AppCompass\FormBuilder\Models\Form;
use AppCompass\AppCompass\Repositories\Eloquent\Repository;

class FormsRepository extends Repository
{
    public function getModel()
    {
        return Form::class;
    }
}
