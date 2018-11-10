<?php

namespace AppCompass\Controllers;

use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\CompaniesRepository;
use AppCompass\Repositories\Criteria\IsCompanyIfNotAdmin;
use AppCompass\Repositories\Criteria\WithUsersCount;

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
