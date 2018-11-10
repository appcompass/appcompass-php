<?php

namespace AppCompass\Controllers;

use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\ByAllowed;
use AppCompass\Repositories\PermissionsRepository;

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
