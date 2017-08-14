<?php

namespace P3in\Repositories;

use P3in\Models\Role;
use P3in\Models\Permission;
use P3in\Interfaces\RolePermissionsRepositoryInterface;

class RolePermissionsRepository extends AbstractChildRepository implements RolePermissionsRepositoryInterface
{
    protected $view_types = ['MultiSelect'];
    const REQUIRES_PERMISSION = 1;

    public function __construct(Permission $model, Role $parent)
    {
        $this->model = $model;

        $this->parent = $parent;

        $this->relationName = 'roles';

        $this->parentToChild = 'permissions';
    }
}
