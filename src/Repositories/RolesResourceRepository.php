<?php

namespace P3in\Repositories;

use P3in\Models\Role;

class RolesResourceRepository extends AbstractResourceRepository
{
    protected $owned_key = 'id';
    protected $view_types = ['Table', 'Card'];
    protected $route_relationships = [
        'company' => 'roles',
        'user' => 'roles',
    ];

    protected $with = [];

    public function getModel()
    {
        return new Role;
    }
}
