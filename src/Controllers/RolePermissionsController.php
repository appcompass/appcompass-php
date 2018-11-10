<?php

namespace AppCompass\Controllers;

use AppCompass\Models\Permission;
use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\HasRole;
use AppCompass\Repositories\PermissionsRepository;

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
