<?php

namespace P3in\Controllers;

use App\Company;
use P3in\Models\Permission;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\Criteria\HasUser;
use P3in\Repositories\PermissionsRepository;

class CompanyUserPermissionsController extends UserPermissionsController
{
    protected $param_name = 'permission';
    protected $view_types = ['MultiSelect'];
    protected $company_id;
    protected $user_id;

    public function __construct(PermissionsRepository $repo)
    {
        $this->repo = $repo;

        $this->company_id = $this->getRouteParam('company');
        $this->user_id = $this->getRouteParam('user');

        $this->repo->pushCriteria(new HasCompany($this->company_id));
        $this->repo->pushCriteria(new HasUser($this->user_id));

        $this->repo->related()
            ->first(Company::class, $this->company_id)
            ->next('users', $this->user_id)
            ->last('permissions')
        ;

        $this->selectable = Permission::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
