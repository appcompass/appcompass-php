<?php

namespace P3in\Repositories\Criteria;

use P3in\Interfaces\RepositoryInterface;

class HasUser extends AbstractCriteria
{
    private $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery()
            ->whereHas('users', function ($query) {
                $query->where('id', $this->user_id);
            })
        ;

        return $query;
    }
}
