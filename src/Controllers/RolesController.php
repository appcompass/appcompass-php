<?php

namespace P3in\Controllers;

use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\ByAllowed;
use P3in\Repositories\RolesRepository;

class RolesController extends AbstractBaseResourceController
{
    protected $param_name = 'role';

    public function __construct(RolesRepository $repo)
    {
        $this->repo = $repo;

        $this->repo->pushCriteria(new ByAllowed());
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
