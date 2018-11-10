<?php

namespace AppCompass\Controllers;

use AppCompass\Policies\AdminOnlyResourcesPolicy;
use AppCompass\Repositories\ResourcesRepository;

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
