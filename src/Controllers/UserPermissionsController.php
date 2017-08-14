<?php

namespace P3in\Controllers;

use P3in\Interfaces\UserPermissionsRepositoryInterface;

class UserPermissionsController extends AbstractChildController
{
    public function __construct(UserPermissionsRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
}
