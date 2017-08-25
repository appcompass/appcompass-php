<?php

namespace P3in\Repositories;

use P3in\Models\Permission;

class PermissionsRepository extends AbstractRepository
{
    const REQUIRES_PERMISSION = 1;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }
}
