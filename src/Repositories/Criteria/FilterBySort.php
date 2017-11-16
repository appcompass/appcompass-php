<?php

namespace P3in\Repositories\Criteria;


use P3in\Interfaces\RepositoryInterface;

class FilterBySort extends AbstractCriteria
{

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery();

        if (request()->has('sorters')) {

            $sorters = request()->sorters;
            if (is_string($sorters)) {
                $sorters = json_decode($sorters, true);
            }

            foreach ((array)$sorters as $field => $order) {
                $query->orderBy($field, $order);
            }
        }

        return $query;
    }
}

// public function sort($sorters = null)
// {
//     if (!request()->has('sorters') && is_null($sorters)) {
//         return;
//     }
//
//     if (is_null($sorters)) {
//         $sorters = request()->sorters;
//         if (is_string($sorters)) {
//             $sorters = json_decode($sorters, true);
//         }
//     }
//
//     foreach ((array)$sorters as $field => $order) {
//         $this->builder->orderBy($field, $order);
//     }
// }