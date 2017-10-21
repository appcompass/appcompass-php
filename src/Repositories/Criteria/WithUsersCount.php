<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;

class WithUsersCount extends AbstractCriteria
{
    public function apply($model, RepositoryInterface $repo)
    {
        //@TODO: change this withCounts to a join because th withCounts runs a query for each record.
        $query = $model->newQuery()->withCount('users');

        return $query;
    }
}
