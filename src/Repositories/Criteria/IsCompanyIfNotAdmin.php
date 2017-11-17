<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;
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

        if ($user->current_company) {
            // $this->company_id = $user->current_company->id;

            $query->where('id', $user->current_company->id);

            return $query;
        }

        $query->where('id', null);

        return $query;
    }
}
