<?php

namespace P3in\Repositories;

use P3in\Models\Role;
use P3in\Repositories\Eloquent\Repository;

class RolesRepository extends Repository
{
    public function getModel()
    {
        return Role::class;
    }
}
