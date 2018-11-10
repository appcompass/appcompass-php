<?php

namespace AppCompass\Repositories;

use App\User;
use AppCompass\Repositories\Eloquent\Repository;

class UsersRepository extends Repository
{
    public function getModel()
    {
        return User::class;
    }
}
