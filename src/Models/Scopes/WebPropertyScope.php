<?php

namespace P3in\Models\Scopes;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class WebPropertyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $thisTable = $model->getTable();

        $builder
            ->join('web_properties', $thisTable . '.web_property_id', '=', 'web_properties.id')
            ->select([
                $thisTable . '.*',
                'web_properties.scheme',
                'web_properties.host',
                'web_properties.name',
                DB::raw('CONCAT(web_properties.scheme, \'://\', web_properties.host) AS url'),
            ]);
    }
}
