<?php

namespace AppCompass\AppCompass\Models\PermissionsRequired\PermissionItems;

use Illuminate\Database\Eloquent\Builder;
use AppCompass\AppCompass\Interfaces\PermissionRequiredItemInterface;
use AppCompass\AppCompass\Models\PermissionsRequired\PermissionItems\PermissionItem;
use AppCompass\AppCompass\Models\Website;

class Controller extends PermissionItem implements PermissionRequiredItemInterface
{
    // Name of the item we're instantiating
    protected $pointer;

    protected $type = 'controller';

    /**
     *  Return the class pointer
     */
    public function pointer()
    {
        return $this->pointer;
    }

    /**
     *  Return permission type
     */
    public function type()
    {
        return $this->type;
    }

    /**
     *  Provides means to fetch target
     */
    public function how(Builder $query)
    {
        return $query
            ->where('website_id', '=', Website::getCurrent()->id)
            ->where('pointer', '=', $this->pointer())
            ->where('type', '=', $this->type())
            ->firstOrFail()
            ->permission;
    }
}
