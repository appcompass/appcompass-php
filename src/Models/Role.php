<?php

namespace AppCompass\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use AppCompass\Traits\HasPermissions;
use AppCompass\Traits\SetsAndChecksPermission;

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
        return $query->where('name', str_replace(' ', '_', strtolower($name)));
    }

    /**
     *   Link roles and users
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'role_user');
    }

    /**
     * Add a User to the Role
     */
    public function addUser(User $user)
    {
        // \Log::info('add user to role: ', [$this->hasUser($user), $this->toArray(), $user->toArray()]);
        if (!$this->hasUser($user)) {
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
        return $this->whereHas('users', function($query) use ($user) {
            $query->where('id', $user->id);
        })->exists();
    }

    public function notify(Notification $notification)
    {
        return \Notification::send($this->users, $notification);
    }
}
