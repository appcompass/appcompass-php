<?php

namespace AppCompass\Observers;

use Illuminate\Database\Eloquent\Model;
use AppCompass\Models\Permission;
use AppCompass\Models\Role;

class PermissionObserver
{
    public function created(Model $model)
    {
        if ($model->name !== Permission::GUEST_PERM_NAME){
            Role::whereName('admin')->firstOrFail()->grantPermission($model);
        }
    }
}
