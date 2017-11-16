<?php

namespace P3in\Controllers;

use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\CompaniesRepository;
use P3in\Repositories\Criteria\IsCompanyIfNotAdmin;
use P3in\Repositories\Criteria\WithUsersCount;

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
