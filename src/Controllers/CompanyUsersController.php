<?php

namespace AppCompass\Controllers;

use AppCompass\Models\Company;
use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\HasCompany;
use AppCompass\Repositories\UsersRepository;

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
