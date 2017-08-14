<?php

namespace P3in\Traits;

use Illuminate\Database\Eloquent\Collection;
use P3in\Models\Permission;
use P3in\Models\PermissionsRequired;

trait HasPermissions
{
    /**
    *   Role permissions
    *
    */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * { function_description }
     *
     * @param      <type>  $perm   The permission
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function grantPermission($perm)
    {
        return $this->grantPermissions($perm);
    }

    /**
      * Grant Permission(s)
      *
      * @param mixed $perm  (string) Permission Type | (Permission) Permission Instance | Collection eleoquent collection of perms to sync | (array)
      */
    public function grantPermissions($perm)
    {
        if (is_null($perm)) {
            return;
        } elseif ($perm instanceof Collection) {
            return $this->permissions()->sync($perm);
        } elseif (is_string($perm)) {
            return $this->grantPermissions(Permission::byName($perm)->firstOrFail());
        } elseif ($perm instanceof Permission) {
            if (!$this->permissions->contains($perm->id)) {
                return $this->permissions()->attach($perm);
            }

            return false;
        } elseif (is_array($perm)) {
            foreach ($perm as $single_permission) {
                $this->grantPermissions($single_permission);
            }
        }
    }

    /**
     *  Revoke all permissions
     */
    public function revokeAll()
    {
        return $this->revokePermissions($this->permissions->lists('name')->toArray());
    }

    /**
     *
     */
    public function revokePermission($perm)
    {
        return $this->revokePermissions($perm);
    }

    /**
      * Revoke permission(s)
      *
      * @param mixed $perm  (string) Permission Type | (Permission) Permission Instance | (array)
      */
    public function revokePermissions($perm)
    {
        if (is_null($perm)) {
            return;
        } elseif (is_string($perm)) {
            return $this->revokePermissions(Permission::byName($perm)->firstOrFail());
        } elseif ($perm instanceof Permission) {
            return $this->permissions()->detach($perm);
        } elseif (is_array($perm)) {
            foreach ($perm as $single_permission) {
                $this->revokePermissions($single_permission);
            }
        }
    }

}
