<?php

namespace AppCompass\Models;

// use AppCompass\Models\Website;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use AppCompass\Interfaces\PermissionRequiredItemInterface as PermissionItemContract;

class PermissionsRequired extends Model
{

    /**
     * Table
     */
    protected $table = 'permissions_required';

    protected $fillable = ['pointer', 'permission_id'];

    /**
     *  A specific permission_required belongs to a website
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     *  Permission_required references a single Permission
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     *
     *  @param mixed owner [route/controller@method@]
     */
    public static function requirePermission(PermissionItemContract $owner, Permission $requires)
    {
        // Enforcing only one permission per item
        $permission_required = PermissionsRequired::firstOrNew([
            'pointer' => $owner->getPointer(),
        ]);

        $permission_required->permission()->associate($requires);

        $permission_required->type = $owner->getType();

        $permission_required->website()->associate(Website::current());

        return $permission_required->save();
    }

    /**
     *  Returns a call to the `how` method on the PermissionItem
     */
    public function scopeRetrieve(Builder $query, PermissionItemContract $perm)
    {
        try {
            $requires = $perm->how($query);

            return $requires;
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}
