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
    public function scopeHavingRole($query, $role)
    {
        $query->whereHas('roles', function ($query) use ($role) {
            switch (true) {
                case $role instanceof Role:
                    $key = 'id';
                    $val = $role->id;
                    break;
                case is_int($role):
                    $key = 'id';
                    $val = $role;
                    break;
                case is_string($role):
                default:
                    $key = 'name';
                    $val = $role;
                    break;
            }
            $query->where($key, $val);
        });
    }

    public function assignRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->assignRole($role);
        }
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
        switch (true) {
            case $role instanceof Role:
                break;
            case is_int($role):
                $role = Role::findOrFail($role);
                break;
            case is_string($role):
            default:
                $role = Role::whereName($role)->firstOrFail();
                break;
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
            } else {
                if (is_string($role)) {
                    $role = Role::whereName($role)->firstOrFail();
                } else {
                    if (is_int($role)) {
                        $role = Role::findOrFail($role);
                    }
                }
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
        // boolean check.
        if (preg_match('/^is/', $method)) {
            return $this->hasRole(snake_case(substr($method, 2)));
        }

        if (preg_match('/^of/', $method)) {
            return $this->havingRole(snake_case(substr($method, 2)));
        }

        return parent::__call($method, $args);
    }
}
