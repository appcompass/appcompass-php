<?php

namespace P3in\Repositories\Criteria\Companies;


use P3in\Interfaces\RepositoryInterface;
use P3in\Repositories\Criteria\Criteria;

class WithUserCount extends Criteria
{

    public function apply($model, RepositoryInterface $repo)
    {
        //@TODO: change this withCounts to a join because th withCounts runs a query for each record.
        $query = $model->newQuery()->withCount('users');

        return $query;
    }
}