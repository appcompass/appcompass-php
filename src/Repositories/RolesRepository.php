<?php

namespace P3in\Repositories;

use P3in\Interfaces\RolesRepositoryInterface;
use P3in\Models\Role;

class RolesRepository extends AbstractRepository implements RolesRepositoryInterface
{
    public $model;
    const REQUIRES_PERMISSION = 1;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }
}
