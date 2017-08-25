<?php

namespace P3in\Repositories;

use P3in\Models\Form;

class FormsRepository extends AbstractRepository
{
    public function __construct(Form $model)
    {
        $this->model = $model;
    }
}
