<?php

namespace P3in\Controllers;

use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\UsersRepository;

class UsersController extends AbstractBaseResourceController
{
    protected $param_name = 'user';

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
        // $company_id = '' // @TODO: get company id of current user if they are not a companies admin.
        // $this->repo->pushCriteria(new HasCompany($company_id));
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
