<?php

namespace AppCompass\Repositories;

use AppCompass\Models\Role;
use AppCompass\Repositories\Eloquent\Repository;

class RolesRepository extends Repository
{
    public function getModel()
    {
        return Role::class;
    }
}
