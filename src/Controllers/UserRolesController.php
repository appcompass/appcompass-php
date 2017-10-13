<?php

namespace P3in\Controllers;

use App\User;
use P3in\Models\Role;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\ExcludeAssignedCompanyRoles;
use P3in\Repositories\Criteria\HasCompany;
use P3in\Repositories\Criteria\HasUser;
use P3in\Repositories\RolesRepository;

class UserRolesController extends UserPermissionsController
{
    protected $param_name = 'role';
    protected $view_types = ['MultiSelect'];

    public function __construct(RolesRepository $repo)
    {
        $this->repo = $repo;

        $this->user_id = $this->getRouteParam('user');

        $this->repo->related()
            ->first(User::class, $this->user_id)
            ->last('roles')
        ;

        // $this->repo->pushCriteria(new HasUser($user_id));

        $this->repo->pushCriteria(new ExcludeAssignedCompanyRoles($this->user_id));

        // $company_id = $this->getRouteParam('company');
        // $this->repo->pushCriteria(new HasCompany($company_id));


        $this->selectable = Role::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }
}
