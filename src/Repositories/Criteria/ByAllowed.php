<?php

namespace P3in\Repositories\Criteria;


use P3in\Interfaces\RepositoryInterface;

class ByAllowed extends AbstractCriteria
{

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery()->byAllowed();

        return $query;
    }
}