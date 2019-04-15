<?php

namespace AppCompass\AppCompass\Controllers;

use AppCompass\AppCompass\Policies\ResourcesPolicy;
use AppCompass\AppCompass\Repositories\Criteria\ByAllowed;
use AppCompass\AppCompass\Repositories\RolesRepository;

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
