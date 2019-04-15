<?php

namespace AppCompass\AppCompass\Repositories;

use AppCompass\AppCompass\Models\Permission;
use AppCompass\AppCompass\Repositories\Eloquent\Repository;

class PermissionsRepository extends Repository
{
    public function getModel()
    {
        return Permission::class;
    }
}
