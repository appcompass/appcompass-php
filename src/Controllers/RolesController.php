<?php

namespace P3in\Controllers;

use P3in\Repositories\RolesRepository;

class RolesController extends AbstractController
{
    public function __construct(RolesRepository $repo)
    {
        $this->repo = $repo;
    }
}
