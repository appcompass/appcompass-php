<?php

namespace P3in\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use P3in\Models\Permission;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\ExcludeAssignedCompanyPermissions;
use P3in\Repositories\Criteria\HasUser;
use P3in\Repositories\PermissionsRepository;

class UserPermissionsController extends AbstractBaseResourceController
{
    protected $param_name = 'permission';
    protected $view_types = ['MultiSelect'];
    protected $user_id;
    protected $company_id;

    public function __construct(PermissionsRepository $repo)
    {
        $this->repo = $repo;

        $this->user_id = $this->getRouteParam('user');

        $this->repo->related()
            ->first(User::class, $this->user_id)
            ->last('permissions')
        ;

        // $this->repo->pushCriteria(new HasUser($this->user_id));

        $this->repo->pushCriteria(new ExcludeAssignedCompanyPermissions($this->user_id));

        // $company_id = $this->getRouteParam('company');
        // $this->repo->pushCriteria(new HasCompany($company_id));

        $this->selectable = Permission::byAllowed()->get();
    }

    public function getPolicy()
    {
        return ResourcesPolicy::class;
    }

    public function store(Request $request)
    {
        // $data = $request->validate($this->rules());
        $data = $request->all();

        $rel = $this->repo->relationship()->getQuery();

        if ($data['removed']) {
            DB::table($rel->getTable())
                ->where('company_id', $this->company_id)
                ->where('user_id', $this->user_id)
                ->whereIn($this->param_name.'_id', $data['removed'])
                ->delete()
            ;
        }

        if (!empty($data['added'])) {
            $formatted = [];
            foreach ($data['added'] as $id) {
                $formatted['added'][$id] = ['company_id' => $this->company_id];
            }
            $this->repo->create($formatted);
        }

        return $this->output(['data' => []]);
    }
}
