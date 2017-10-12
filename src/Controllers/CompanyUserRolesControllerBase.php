<?php

namespace P3in\Controllers;

use P3in\Repositories\RolesResourceRepository;

class CompanyUserRolesControllerBase extends BaseResourceController
{
    public function __construct(RolesResourceRepository $repo)
    {
        $this->repo = $repo;
    }
}
