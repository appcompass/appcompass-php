<?php

namespace AppCompass\AppCompass\Controllers;

use AppCompass\AppCompass\Policies\AdminOnlyResourcesPolicy;
use AppCompass\AppCompass\Repositories\ResourcesRepository;

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
