<?php

namespace AppCompass\AppCompass\Repositories\Criteria;

use AppCompass\AppCompass\Interfaces\RepositoryInterface;
use Auth;

class IsCompanyIfNotAdmin extends AbstractCriteria
{

    // protected $company_id;

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery();

        $user = Auth::user();

        if ($user->isAdmin()){
            return $query;
        }

        if ($user->hasPermission('all_users_admin')) {
            return $query;
        }

        foreach ($user->roles as $role) {
            if($role->hasPermission('all_users_admin')){
                return $query;
            }
        }

        if ($user->current_company) {

            $query->where('id', $user->current_company->id);

            return $query;
        }

        $query->where('id', null);

        return $query;
    }
}
