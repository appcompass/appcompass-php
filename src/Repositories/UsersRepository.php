<?php

namespace P3in\Repositories;

use App\User;
use P3in\Repositories\Eloquent\Repository;

class UsersRepository extends Repository
{
    public function getModel()
    {
        return User::class;
    }
}
