<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;
use Auth;

class HasCompanyIfNotAdmin extends AbstractCriteria
{
    protected $company_id;

    public function apply($model, RepositoryInterface $repo)
    {
        $user = Auth::user();

        if ($user->current_company) {
            $this->company_id = $user->current_company->id;
        }

        $query = $model->newQuery();

        if (!$user->isAdmin() && $this->company_id){
            $query->whereHas('companies', function ($query) {
                    $query->where('id', $this->company_id);
                })
            ;
        }

        return $query;
    }
}
