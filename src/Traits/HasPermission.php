<?php

namespace AppCompass\AppCompass\Traits;

use AppCompass\AppCompass\Models\Permission;

trait HasPermission
{
    use SetsAndChecksPermission;

    public function permissionFieldName()
    {
        return 'req_perm';
    }

    public function permissionRelationshipName()
    {
        return 'permission';
    }

    public function allowNullPermission()
    {
        return false;
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, $this->permissionFieldName());
    }
}
