<?php

namespace P3in\Controllers;

use P3in\Repositories\PermissionsRepository;

class PermissionsController extends AbstractController
{
    public function __construct(PermissionsRepository $repo)
    {
        $this->repo = $repo;
    }
}
