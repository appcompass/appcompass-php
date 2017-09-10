<?php

namespace P3in\Observers;

use Illuminate\Database\Eloquent\Model;
use P3in\Models\Permission;
use P3in\Models\Role;

class PermissionObserver
{
    public function created(Model $model)
    {
        if ($model->name !== Permission::GUEST_PERM_NAME){
            Role::whereName('admin')->firstOrFail()->grantPermission($model);
        }
    }
}
