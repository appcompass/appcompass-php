<?php

namespace P3in\Controllers;

use P3in\Models\Company;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\UsersRepository;

class CompanyUsersController extends AbstractBaseResourceController
{
    protected $param_name = 'user';
    protected $company_id;
    protected $user_id;

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;

        $this->company_id = $this->getRouteParam('company');
        $this->user_id = $this->getRouteParam('user');

        $this->repo->pushCriteria(new HasCompany($this->company_id));

        $this->repo->related()
            ->first(Company::class, $this->company_id)
            ->last('users')
        ;

    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
