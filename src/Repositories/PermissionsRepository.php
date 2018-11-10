<?php

namespace AppCompass\Repositories;

use AppCompass\Models\Permission;
use AppCompass\Repositories\Eloquent\Repository;

class PermissionsRepository extends Repository
{
    public function getModel()
    {
        return Permission::class;
    }
}
