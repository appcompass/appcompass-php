<?php

namespace P3in\Controllers;

use P3in\Repositories\RolePermissionsRepository;

class RolePermissionsController extends AbstractChildController
{
    public function __construct(RolePermissionsRepository $repo)
    {
        $this->repo = $repo;
    }
}
