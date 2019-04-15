<?php

namespace AppCompass\AppCompass\Models\PermissionsRequired\PermissionItems;

use Illuminate\Database\Eloquent\Builder;
use AppCompass\AppCompass\Interfaces\PermissionRequiredItemInterface;
use AppCompass\AppCompass\Models\PermissionsRequired\PermissionItems\PermissionItem;
use AppCompass\AppCompass\Models\Website;

class Element extends PermissionItem implements PermissionRequiredItemInterface
{

    // Name of the item we're
    protected $pointer;

    protected $type = 'element';
}
