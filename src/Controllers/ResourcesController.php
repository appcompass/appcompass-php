<?php

namespace P3in\Controllers;

use P3in\Policies\AdminOnlyResourcesPolicy;
use P3in\Repositories\ResourcesRepository;

class ResourcesController extends AbstractBaseResourceController
{
    protected $param_name = 'resource';

    public function __construct(ResourcesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPolicy()
    {
        return AdminOnlyResourcesPolicy::class;
    }
}
