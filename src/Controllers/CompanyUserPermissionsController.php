<?php

namespace P3in\Controllers;

use P3in\Models\Permission;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\Criteria\HasUser;
use P3in\Repositories\PermissionsRepository;

class CompanyUserPermissionsController extends AbstractBaseResourceController
{
    protected $param_name = 'permission';
    protected $view_types = ['MultiSelect'];

    public function __construct(PermissionsRepository $repo)
    {
        $this->repo = $repo;

        $company_id = $this->getRouteParam('company');
        $user_id = $this->getRouteParam('user');

        $this->repo->pushCriteria(new HasCompany($company_id));
        $this->repo->pushCriteria(new HasUser($user_id));

        $this->selectable = Permission::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
