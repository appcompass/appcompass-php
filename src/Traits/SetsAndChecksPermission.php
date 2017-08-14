<?php

namespace P3in\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use P3in\Models\Permission;
use P3in\Models\User;

trait SetsAndChecksPermission
{
    abstract public function permissionFieldName();

    abstract public function permissionRelationshipName();

    abstract public function allowNullPermission();

    // @TODO: clean/break this up a bit.
    public function setPermission($name)
    {
        try {
            if ($name instanceof Permission) {
                $permission = $name;
            } elseif (is_string($name)) {
                $permission = Permission::byName($name)->firstOrFail();
            } elseif (is_int($name)) {
                $permission = Permission::findOrFail($name);
            } else {
                // throw error?
                return $this;
            }
        } catch (ModelNotFoundException $e) {
            $label = ucwords(str_replace(['-', '_'], ' ', $name));
            $permission = Permission::create([
                'name'        => $name,
                'label'       => $label,
                'description' => $label . ' Permission',
            ]);
        }

        $this->{$this->permissionRelationshipName()}()->associate($permission);

        $this->save();

        return $this;
    }

    public function scopeByAllowed(Builder $query, User $user = null)
    {
        $query->where(function ($query) use ($user) {
            if (!$user && Auth::check()) {
                $user = Auth::user();
            }
            if ($this->allowNullPermission() || $user->isAdmin()) {
                $query->whereNull($this->permissionFieldName());
            }
            $query->orWhereHas($this->permissionRelationshipName(), function ($query) {
                $query->where('name', 'guest');
            });

            if ($user) {
                // the cache way
                $permIds = (array)Cache::tags('auth_permissions')->get($user->id);
                $query->orWhereIn($this->permissionFieldName(), $permIds);
                // the query way.
                // $query->orWhereHas($this->permissionRelationshipName(), function ($query) use ($user) {
                //     $query->whereHas('users', function($query) use ($user) {
                //         $query->where('user_id', $user->id);
                //     })->orWhereHas('roles', function($query) use ($user) {
                //         $query->orWhereHas('users', function($query) use ($user) {
                //             $query->where('user_id', $user->id);
                //         });
                //     });
                // });
            }
        });
    }
}
