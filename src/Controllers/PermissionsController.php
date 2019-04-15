<?php

namespace AppCompass\AppCompass\Controllers;

use AppCompass\AppCompass\Policies\ResourcesPolicy;
use AppCompass\AppCompass\Repositories\Criteria\ByAllowed;
use AppCompass\AppCompass\Repositories\PermissionsRepository;

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
