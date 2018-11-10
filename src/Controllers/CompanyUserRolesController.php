<?php

namespace AppCompass\Controllers;

use App\Company;
use AppCompass\Models\Role;
use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\HasCompany;
use AppCompass\Repositories\Criteria\HasUser;
use AppCompass\Repositories\RolesRepository;

class CompanyUserRolesController extends UserPermissionsController
{
    protected $param_name = 'role';
    protected $view_types = ['MultiSelect'];

    public function __construct(RolesRepository $repo)
    {
        $this->repo = $repo;

        $this->company_id = $this->getRouteParam('company');
        $this->user_id = $this->getRouteParam('user');

        $this->repo->pushCriteria(new HasCompany($this->company_id));
        $this->repo->pushCriteria(new HasUser($this->user_id));

        $this->repo->related()
            ->first(Company::class, $this->company_id)
            ->next('users', $this->user_id)
            ->last('roles')
        ;

        $this->selectable = Role::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
