<?php

namespace AppCompass\AppCompass\Observers;

use Illuminate\Database\Eloquent\Model;
use AppCompass\AppCompass\Models\Permission;
use AppCompass\AppCompass\Models\Role;

class PermissionObserver
{
    public function created(Model $model)
    {
        if ($model->name !== Permission::GUEST_PERM_NAME){
            Role::whereName('admin')->firstOrFail()->grantPermission($model);
        }
    }
}
