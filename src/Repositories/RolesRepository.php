<?php

namespace AppCompass\AppCompass\Repositories;

use AppCompass\AppCompass\Models\Role;
use AppCompass\AppCompass\Repositories\Eloquent\Repository;

class RolesRepository extends Repository
{
    public function getModel()
    {
        return Role::class;
    }
}
