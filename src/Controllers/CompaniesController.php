<?php

namespace AppCompass\AppCompass\Controllers;

use AppCompass\AppCompass\Policies\ResourcesPolicy;
use AppCompass\AppCompass\Repositories\CompaniesRepository;
use AppCompass\AppCompass\Repositories\Criteria\IsCompanyIfNotAdmin;
use AppCompass\AppCompass\Repositories\Criteria\WithUsersCount;

class CompaniesController extends AbstractBaseResourceController
{
    protected $param_name = 'company';

    public function __construct(CompaniesRepository $repo)
    {
        $this->repo = $repo;
        $this->repo->pushCriteria(new WithUsersCount());
        $this->repo->pushCriteria(new IsCompanyIfNotAdmin());
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
