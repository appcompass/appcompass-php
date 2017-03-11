<?php

namespace P3in\Repositories;

use P3in\Models\User;
use P3in\Models\Role;
use P3in\Interfaces\UserRolesRepositoryInterface;

class UserRolesRepository extends AbstractChildRepository implements UserRolesRepositoryInterface
{
    public function __construct(Role $model, User $parent)
    {
        $this->model = $model;

        $this->parent = $parent;

        $this->relationName = 'users';

        $this->parentToChild = 'roles';
    }
}
