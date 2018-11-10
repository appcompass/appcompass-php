<?php

namespace AppCompass\Models\PermissionsRequired\PermissionItems;

use Illuminate\Database\Eloquent\Builder;
use AppCompass\Interfaces\PermissionRequiredItemInterface;
use AppCompass\Models\PermissionsRequired\PermissionItems\PermissionItem;
use AppCompass\Models\Website;

class Element extends PermissionItem implements PermissionRequiredItemInterface
{

    // Name of the item we're
    protected $pointer;

    protected $type = 'element';
}
