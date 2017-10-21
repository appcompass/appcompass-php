<?php

namespace P3in\Controllers;

use P3in\Models\Permission;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasRole;
use P3in\Repositories\PermissionsRepository;

class RolePermissionsController extends AbstractBaseResourceController
{
    protected $param_name = 'permission';
    protected $view_types = ['MultiSelect'];

    public function __construct(PermissionsRepository $repo)
    {
        $this->repo = $repo;

        $role_id = $this->getRouteParam('role');
        $this->repo->pushCriteria(new HasRole($role_id));

        // $company_id = $this->getRouteParam('company');
        // $this->repo->pushCriteria(new HasCompany($company_id));

        $this->selectable = Permission::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
