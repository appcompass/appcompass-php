<?php

namespace AppCompass\AppCompass\Controllers;

use App\Company;
use AppCompass\AppCompass\Models\Permission;
use AppCompass\AppCompass\Policies\ResourcesPolicy;
use AppCompass\AppCompass\Repositories\Criteria\HasCompany;
use AppCompass\AppCompass\Repositories\Criteria\HasUser;
use AppCompass\AppCompass\Repositories\PermissionsRepository;

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
