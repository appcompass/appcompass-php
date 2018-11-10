<?php

namespace AppCompass\Models\PermissionsRequired\PermissionItems;

use Illuminate\Database\Eloquent\Builder;
use AppCompass\Interfaces\PermissionRequiredItemInterface;
use AppCompass\Models\PermissionsRequired\PermissionItems\PermissionItem;
use AppCompass\Models\Website;

class Model extends PermissionItem implements PermissionRequiredItemInterface
{
    // Name of the item we're instantiating
    protected $pointer;

    protected $type = 'model';
}
