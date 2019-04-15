<?php

namespace AppCompass\AppCompass\Controllers;

use AppCompass\AppCompass\Policies\AdminOnlyResourcesPolicy;
use AppCompass\AppCompass\Repositories\FormsRepository;

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
