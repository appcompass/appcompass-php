<?php

namespace P3in\Repositories;

use P3in\Models\Permission;

class PermissionsResourceRepository extends AbstractResourceRepository
{
    protected $owned_key = 'id';
    protected $view_types = ['Table', 'Card'];
    protected $route_relationships = [
        'company' => 'permissions',
        'user' => 'permissions',
        'role' => 'permissions',
        'permission' => 'permissions',
    ];

    protected $with = [];

    public function getModel()
    {
        return new Permission;
    }
}
