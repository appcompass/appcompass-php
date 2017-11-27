<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;
use P3in\Models\User;

class HasCompanyIfNotAdmin extends AbstractCriteria
{
    protected $company_id;
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery();

        if ($this->user->isAdmin()) {
            return $query;
        }

        if ($this->user->hasPermission('all_users_admin')) {
            return $query;
        }

        if ($this->user->current_company) {
            $this->company_id = $this->user->current_company->id;

            $query->whereHas('companies', function ($query) {
                $query->where('id', $this->company_id);
            });
        } else {
            $query->where('id', null);
        }

        return $query;
    }
}
