<?php

namespace AppCompass\Repositories\Criteria;


use AppCompass\Interfaces\RepositoryInterface;

class ByAllowed extends AbstractCriteria
{

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery()->byAllowed();

        return $query;
    }
}