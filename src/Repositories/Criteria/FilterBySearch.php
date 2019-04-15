<?php

namespace AppCompass\AppCompass\Repositories\Criteria;


use AppCompass\AppCompass\Interfaces\RepositoryInterface;

class FilterBySearch extends AbstractCriteria
{

    public function apply($model, RepositoryInterface $repo)
    {
        $query = $model->newQuery();

        if (request()->has('search')) {
            // request sometimes sends empty object string '{}' instead of an
            // actual object, absolute query string values and what not.
            $search = json_decode(request()->search, true);

            if (is_string($search)) {
                $search = json_decode($search, true);
            }

            foreach ((array) $search as $column => $string) {
                $string = strtolower($string);
                $string = $query->getConnection()->getPdo()->quote("%$string%");
                $query->whereRaw("lower(cast($column as varchar)) LIKE {$string}");
            }

        }

        return $query;
    }
}
