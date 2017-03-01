<?php

namespace P3in\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GalleryItemScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('type', $model->getType());
    }
}
