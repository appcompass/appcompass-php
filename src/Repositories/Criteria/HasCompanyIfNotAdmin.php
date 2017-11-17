<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;
use Auth;

class HasCompanyIfNotAdmin extends AbstractCriteria
{
    protected $company_id;

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery();

        $user = Auth::user();

        if ($user->isAdmin()){
            return $query;
        }

        if ($user->current_company) {
            $this->company_id = $user->current_company->id;


            if($user->hasPermission('own_users_admin')) {
                $query->whereHas('companies', function ($query) {
                    $query->where('id', $this->company_id);
                })
                ;
            }

            return $query;
        }
        
        $query->where('id', null);

        return $query;

    }
}
