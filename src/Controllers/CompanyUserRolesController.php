<?php

namespace P3in\Controllers;

use P3in\Models\Role;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\Criteria\HasUser;
use P3in\Repositories\RolesRepository;

class CompanyUserRolesController extends AbstractBaseResourceController
{
    protected $param_name = 'role';
    protected $view_types = ['MultiSelect'];

    public function __construct(RolesRepository $repo)
    {
        $this->repo = $repo;

        $company_id = $this->getRouteParam('company');
        $user_id = $this->getRouteParam('user');

        $this->repo->pushCriteria(new HasCompany($company_id));
        $this->repo->pushCriteria(new HasUser($user_id));

        $this->selectable = Role::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
