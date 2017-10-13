<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;

class HasCompany extends AbstractCriteria
{
    private $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery()
            ->whereHas('companies', function ($query) {
                $query->where('id', $this->company_id);
            })
        ;

        return $query;
    }
}
