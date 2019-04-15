<?php

namespace AppCompass\AppCompass\Repositories;

use AppCompass\AppCompass\Models\User;
use AppCompass\AppCompass\Repositories\Eloquent\Repository;

class UsersRepository extends Repository
{
    public function getModel()
    {
        return User::class;
    }
}
