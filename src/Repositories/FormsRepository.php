<?php

namespace P3in\Repositories;

use P3in\Interfaces\FormsRepositoryInterface;
use P3in\Models\Form;

class FormsRepository extends AbstractRepository implements FormsRepositoryInterface
{
    public function __construct(Form $model)
    {
        $this->model = $model;
    }
}
