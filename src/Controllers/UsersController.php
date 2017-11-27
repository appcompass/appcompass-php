<?php

namespace P3in\Controllers;

use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompanyIfNotAdmin;
use P3in\Repositories\UsersRepository;
use Auth;

class UsersController extends AbstractBaseResourceController
{
    protected $param_name = 'user';

    public function __construct(UsersRepository $repo)
    {
        $this->repo = $repo;
        $this->repo->pushCriteria(new HasCompanyIfNotAdmin(Auth::user()));
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
