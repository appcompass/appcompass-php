<?php

namespace AppCompass\Traits;

use AppCompass\Models\Role;

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
        return $this;
    }

    /**
     *  Assign role to current model.
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

        if (!$this->hasRole($role)) {
            $this->roles()->attach($role);
        }
        return $this;
    }

    /**
     *  Remove role form current model.
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
        return $this->where('id', $this->id) // @TODO: seems to be a bug in Laravel?  report it and see what comes of it.
            ->whereHas('roles', function ($query) use ($role) {
            $field = 'id';
            $value = null;
            if ($role instanceof Role) {
                $field = 'id';
                $value = $role->id;
            } elseif (is_string($role)) {
                $field = 'name';
                $value = $role;
            } elseif (is_int($role)) {
                $field = 'id';
                $value = $role;
            }
            $query->where($field, $value);
        })->exists()
            ;
    }

    /**
     *
     */
    public function hasUser(User $user)
    {
        return $this->whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->exists()
            ;
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
