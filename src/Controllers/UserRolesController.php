<?php

namespace P3in\Controllers;

use P3in\Repositories\UserRolesRepository;

class UserRolesController extends AbstractChildController
{
    public function __construct(UserRolesRepository $repo)
    {
        $this->repo = $repo;
    }
}
