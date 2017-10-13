<?php

namespace P3in\Controllers;

use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\ByAllowed;
use P3in\Repositories\PermissionsRepository;

class PermissionsController  extends AbstractBaseResourceController
{
    protected $param_name = 'permission';

    public function __construct(PermissionsRepository $repo)
    {
        $this->repo = $repo;

        $this->repo->pushCriteria(new ByAllowed());
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
