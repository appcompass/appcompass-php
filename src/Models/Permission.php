<?php

namespace AppCompass\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use AppCompass\Traits\SetsAndChecksPermission;
use App\Company;

class Permission extends Model
{
    use SetsAndChecksPermission;

    const GUEST_PERM_NAME = 'guest';
    const LOGGED_IN_PERM_NAME = 'logged-user';

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'label',
        'description',
        'system',
    ];

    /**
     *  Model Rules
     *
     */
    public static $rules = [
        'name'  => 'required',
        'label' => 'required',
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

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'permission_user');
    }

    /**
     *   Get roles having this permission
     *
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public static function getAuthPerms()
    {
        //@TODO: we should probably perma cache the guest ID so it's the same flow every time.
        if (Auth::check()) {
            return (array) Cache::tags('auth_permissions')->get(Auth::user()->id);
        } else {
            return static::byName(static::GUEST_PERM_NAME)->get()->pluck('id')->toArray();
        }
    }
    /**
     *
     *
     * @param      \Illuminate\Database\Eloquent\Builder $builder The builder
     * @param      \App\User                             $user    The user
     *
     * @return     <type>                                 ( description_of_the_return_value )
     */
    // public function scopeOf(Builder $builder, User $user)
    // {
    //     return $builder->where('user_id', $user->id);
    // }

    /**
     *   Get permission by name
     *
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }
}
