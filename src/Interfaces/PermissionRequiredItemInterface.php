<?php

namespace AppCompass\AppCompass\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface PermissionRequiredItemInterface
{

    /**
     * How
     */
    public function how(Builder $query);
}
