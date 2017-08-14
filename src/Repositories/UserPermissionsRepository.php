<?php

namespace P3in\Repositories;

use P3in\Models\User;
use P3in\Models\Permission;
use P3in\Interfaces\UserPermissionsRepositoryInterface;

class UserPermissionsRepository extends AbstractChildRepository implements UserPermissionsRepositoryInterface
{
    protected $view_types = ['MultiSelect'];
    const REQUIRES_PERMISSION = 1;

    public function __construct(Permission $model, User $parent)
    {
        $this->model = $model;

        $this->parent = $parent;

        $this->relationName = 'users';

        $this->parentToChild = 'permissions';
    }
}
