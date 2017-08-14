<?php

namespace P3in\Traits;

use P3in\Models\Role;

trait HasRoles
{

    /**
     *  Get all the roles this user belongs to
     *
     *
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Get users having a specific rol e
     *
     * @param      <type>  $query  The query
     * @param      <type>  $role   The role
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function scopeHavingRole($query, $role)
    {
        return Role::whereName($role)->firstOrFail()->users();
    }

    /**
     * Add current user to a role
     *
     * @param      mixed $role The role
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function assignRole($role)
    {
        if ($role instanceof Role) {
            // do nothing.
        } else {
            if (is_int($role)) {
                $role = Role::findOrFail($role);
            } else {
                if (is_string($role)) {
                    $role = Role::whereName($role)->firstOrFail();
                }
            }
        }

        return $role->addUser($this);
    }

    /**
     *  Remove current user from a role
     */
    public function revokeRole(Role $role)
    {
        return $role->removeUser($this);
    }

    public function hasAnyRoles($roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if it has role.
     *
     * @param      <type>   $role  The role
     *
     * @return     boolean  True if has role, False otherwise.
     */
    public function hasRole($role)
    {
        try {
            if ($role instanceof Role) {
                // do nothing.
            } elseif (is_string($role)) {
                $role = Role::whereName($role)->firstOrFail();
            } elseif (is_int($role)) {
                $role = Role::findOrFail($role);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return false;
        }

        return $role->hasUser($this);
    }

    /**
     * Allows for role/group matching using  is[name] pattern
     *
     * @param      <type>  $method  The method
     * @param      <type>  $args    The arguments
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function __call($method, $args)
    {
        if (preg_match('/^is/', $method)) {
            return $this->hasRole(lcfirst(substr($method, 2)));
        }

        return parent::__call($method, $args);
    }
}
