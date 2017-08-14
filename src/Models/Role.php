<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use P3in\Traits\HasPermissions;
use P3in\Traits\SetsAndChecksPermission;

class Role extends Model
{
    use HasPermissions, SetsAndChecksPermission;

    protected $fillable = [
        'name',
        'label',
        'description',
        'active',
    ];

    public function permissionFieldName()
    {
        return 'assignable_by_id';
    }

    public function permissionRelationshipName()
    {
        return 'assignable_by';
    }

    public function allowNullPermission()
    {
        return false;
    }

    public function assignable_by()
    {
        return $this->belongsTo(Permission::class, $this->permissionFieldName());
    }

    /**
     * Get a role by name
     */
    public function scopeByName(Builder $query, $name)
    {
        return $query->where('name', str_replace(' ', '-', strtolower($name)));
    }

    /**
     *   Link roles and users
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Add a User to the Role
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user->id)) {
            return $this->users()->attach($user);
        }

        return false;
    }

    /**
     * remove a user from this role
     */
    public function removeUser(User $user)
    {
        return $this->users()->detach($user);
    }

    /**
     *
     */
    public function hasUser(User $user)
    {
        return $this->users->contains($user->id);
    }

    public function notify(Notification $notification)
    {
        return \Notification::send($this->users, $notification);
    }
}
