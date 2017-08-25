<?php

namespace P3in\Controllers;

use P3in\Repositories\UserPermissionsRepository;

class UserPermissionsController extends AbstractChildController
{
    public function __construct(UserPermissionsRepository $repo)
    {
        $this->repo = $repo;
    }
}
