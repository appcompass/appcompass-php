<?php

namespace AppCompass\AppCompass\Repositories\Criteria;

use AppCompass\AppCompass\Interfaces\RepositoryInterface;

class WithUsersCount extends AbstractCriteria
{
    public function apply($model, RepositoryInterface $repo)
    {
        //@TODO: change this withCounts to a join because th withCounts runs a query for each record.
        $query = $model->newQuery()->withCount('users');

        return $query;
    }
}
