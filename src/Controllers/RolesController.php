<?php

namespace AppCompass\Controllers;

use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\ByAllowed;
use AppCompass\Repositories\RolesRepository;

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
