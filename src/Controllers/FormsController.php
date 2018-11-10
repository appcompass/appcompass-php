<?php

namespace AppCompass\Controllers;

use AppCompass\Policies\AdminOnlyResourcesPolicy;
use AppCompass\Repositories\FormsRepository;

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
