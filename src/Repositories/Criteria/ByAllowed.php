<?php

namespace AppCompass\AppCompass\Repositories\Criteria;


use AppCompass\AppCompass\Interfaces\RepositoryInterface;

class ByAllowed extends AbstractCriteria
{

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery()->byAllowed();

        return $query;
    }
}