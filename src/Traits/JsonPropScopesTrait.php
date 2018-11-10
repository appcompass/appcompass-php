<?php

/**
*   Provides scopes for dealing with Json Properties matching
*
*   for extensive coverage look at JsonPropPgTrait.php
*
*/

namespace AppCompass\Traits;

trait JsonPropScopesTrait
{

    /**
    *   Returns item by Model
    *
    *
    */
    public function scopeByModel($query, $model)
    {
        return $query->where('model', '=', $model);
    }

    /**
    *   Adds props to the query
    *
    *   props are mapped to json field "props"
    *
    */
    public function scopeWithProps($query, $props = null)
    {
        if (is_array($props) && count($props)) {
            $props = trim(implode(' AND ', $props));

            return $query->whereRaw($props);
        }

        return $query;
    }
}
