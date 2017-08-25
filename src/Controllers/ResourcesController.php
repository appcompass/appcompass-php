<?php

namespace P3in\Controllers;

use P3in\Repositories\ResourcesRepository;

class ResourcesController extends AbstractController
{
    public function __construct(ResourcesRepository $repo)
    {
        $this->repo = $repo;
    }
}
