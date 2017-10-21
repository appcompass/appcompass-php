<?php

namespace P3in\Controllers;

use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\UsersRepository;

class CompanyUsersController extends AbstractBaseResourceController
{
    protected $param_name = 'user';

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;

        $company_id = $this->getRouteParam('company');

        $this->repo->pushCriteria(new HasCompany($company_id));
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
