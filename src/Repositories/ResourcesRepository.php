<?php

namespace P3in\Repositories;

use P3in\Models\Resource;

class ResourcesRepository extends AbstractRepository
{
    public $model;

    public function __construct(Resource $model)
    {
        $this->model = $model;
    }
}
