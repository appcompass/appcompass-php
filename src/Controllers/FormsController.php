<?php

namespace P3in\Controllers;

use P3in\Policies\AdminOnlyResourcesPolicy;
use P3in\Repositories\FormsRepository;

class FormsController extends AbstractBaseResourceController
{
    protected $param_name = 'form';

    public function __construct(FormsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getPolicy()
    {
        return AdminOnlyResourcesPolicy::class;
    }
}
