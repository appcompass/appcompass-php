<?php

namespace P3in\Controllers;

use P3in\Repositories\Criteria\Users\OfCompany;
use P3in\Repositories\Criteria\Users\OfUser;
use P3in\Repositories\UsersRepository;

class CompanyUsersController extends BaseResourceController
{
    protected $param_name = 'user';

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;

        $company_id = $this->getRouteParam('company');

        $this->repo->pushCriteria(new OfCompany($company_id));
    }

}
