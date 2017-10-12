<?php

namespace P3in\Repositories;

use App\User;
use P3in\Repositories\Eloquent\Repository;

class UsersRepository extends Repository
{
    protected $owned_key = 'id';
    protected $view_types = ['Table', 'Card'];
    protected $route_relationships = [
        'company' => 'users',
        'role' => 'users',
        'permission' => 'users',
    ];

    protected $with = ['roles'];

    public function getModel()
    {
        return User::class;
    }
}
