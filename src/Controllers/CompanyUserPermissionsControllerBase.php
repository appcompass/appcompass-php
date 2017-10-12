<?php

namespace P3in\Controllers;

use P3in\Repositories\PermissionsResourceRepository;

class CompanyUserPermissionsControllerBase extends BaseResourceController
{
    public function __construct(PermissionsResourceRepository $repo)
    {
        $this->repo = $repo;
    }
}
