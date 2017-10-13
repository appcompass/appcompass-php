<?php

namespace P3in\Repositories;

use P3in\Models\Permission;
use P3in\Repositories\Eloquent\Repository;

class PermissionsRepository extends Repository
{
    public function getModel()
    {
        return Permission::class;
    }
}
