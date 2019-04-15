<?php

namespace AppCompass\AppCompass\Controllers;

use AppCompass\AppCompass\Models\Company;
use AppCompass\AppCompass\Policies\ResourcesPolicy;
use AppCompass\AppCompass\Repositories\Criteria\HasCompany;
use AppCompass\AppCompass\Repositories\UsersRepository;

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
