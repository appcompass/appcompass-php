<?php

namespace AppCompass\Controllers;

use App\User;
use AppCompass\Models\Role;
use AppCompass\Policies\ResourcesPolicy;
use AppCompass\Repositories\Criteria\ExcludeAssignedCompanyRoles;
use AppCompass\Repositories\Criteria\HasCompany;
use AppCompass\Repositories\Criteria\HasUser;
use AppCompass\Repositories\RolesRepository;

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
