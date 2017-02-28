<?php

namespace P3in\Models;

use Cache;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Modular;
use P3in\Models\Gallery;
use P3in\Models\Group;
use P3in\Models\Permission;
use P3in\Models\Photo;
use P3in\ModularBaseModel;
use P3in\Traits\HasPermissions;
use P3in\Traits\HasProfileTrait;

class User extends ModularBaseModel implements
    AuthenticatableContract,
    AuthenticatableUserContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use
        Authenticatable,
        Authorizable,
        CanResetPassword,
        Notifiable,
        HasPermissions,
        HasProfileTrait
        ;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Specifiy the connectin for good measure
     */
    protected $connection = 'pgsql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'active',
        'system_user',
    ];

    /**
     *  The attributes excluded from the model's JSON form.
     *
     *  @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'system',
        'activated_at',
        'activation_code'
    ];

    /**
     * Stuff to append to each request
     *
     *
     */
    protected $appends = ['full_name', 'gravatar_url'];

    public static $rules = [
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'phone' => 'required|max:255',
        'email' => 'required|email|unique:users|max:255',
        'password' => 'required|min:2|max:255',
    ];

    /**
     *  Get all the groups this user belongs to
     *
     *
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    /**
     * Permissions
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * Photos
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Galleries
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     *  Get either all or a specific profile type of a user
     *
     *
     *  TODO: this needs to be refactored a little
     */
    public function linkProfile(Model $model)
    {
        $profile = $this->profiles()->firstOrNew([
            'profileable_id' => $model->getKey(),
            'profileable_type' => get_class($model),
        ]);
        $profile->save();
    }

    /**
     * { function_description }
     *
     * @param      <type>  $model_name  The model name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function profile($model_name)
    {
        $base_profile = $this->profiles()->where('profileable_type', $model_name)->first();

        return $base_profile ? $base_profile->profileable : null;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $field_name  The field name
     *
     * @return     array   ( description_of_the_return_value )
     */
    public function populateField($field_name)
    {
        switch ($field_name) {
            case 'users_list':
                $users = User::select(\DB::raw("concat(first_name,' ',last_name) as name"), 'id')->get();

                return $users->pluck('name', 'id');
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * Sets the password attribute.
     *
     * @param      <type>  $value  The value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     *  Get user's full name
     *
     */
    public function getFullNameAttribute()
    {
        return sprintf("%s %s", $this->first_name, $this->last_name);
    }

    /**
     *  Get user's full name
     *
     */
    public function getGravatarUrlAttribute()
    {
        return "https://www.gravatar.com/avatar/" . md5($this->email) . '?d=identicon';
    }

    /**
     *  Is user root
     */
    public function isRoot()
    {
        return Group::administrators()->users
            ->contains($this->id);
    }

    /**
     *  Is user a manager
     *  @TODO isn't manager a bit too specific? maybe not
     */
    public function isManager()
    {
        return Group::managers()->users
            ->contains($this->id);
    }

    /**
     *  Whether current user a system user
     */
    public function isSystem()
    {
        return $this->system_user;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
             'user' => [
                'id' => $this->id,
                'name' => $this->full_name,
             ]
        ];
    }

    /**
     *  Get/Set user's Avatar
     *
     */
    public function avatar(Photo $photo = null, $size = 48)
    {
        if (is_null($photo)) {
            return "//www.gravatar.com/avatar/" . md5($this->email) . "?d=identicon&s={$size}";
        }

        // if (! Modular::isDef('photos')) {

        //     $userEmail = \Auth::user()->email;

        // }

        // if (! is_null($photo)) {

        //     if (! is_null($this->avatar)) {

        //         $this->avatar()
        //             ->first()
        //             ->unlink();

        //     }

        //     $this->avatar()->save($photo);

        // }

        // return $this->morphOne(Photo::class, 'photoable');
    }

    public static function scopeSystemUsers($query)
    {
        return $query->where('system', true)->orderBy('id', 'asc');
    }
    /**
      * Add current user to a group
      */
    // public function addToGroup(Group $group)
    // {
    //     return $group->addUser($this);
    // }

    /**
      *  Remove current user from a group
      */
    // public function removeFromGroup(Group $group)
    // {
    //     return $group->removeUser($this);
    // }

    /**
     * Return all the permissions of the user
     *
     * @return array permission owned by the user
     */
    // public function allPermissions()
    // {

        // @TODO  add getCacheKey or something, the id is overly non-specific. should return class_name . id . updated_at_timestamp

        // return Cache::tags('user_permissions')->remember('user_'.$this->id.'_'.$this->updated_at, 1, function() {

            // $this->load(['groups.permissions' => function($query) { $query->where('active', true); }])
            // $this->load('groups.permissions')
            //     ->load('permissions');

    //         $perms = collect($this->permissions->lists('type', 'id'));

    //         $this->groups
    //             ->each(function($group) use($perms) {
    //                 $group->permissions
    //                     ->lists('type', 'id')
    //                     ->each(function($perm, $key) use($perms) {
    //                         $perms->push($perm);
    //                     });
    //         });

    //         return $perms->unique();

    //     });

    // }

    /**
     * Adds a permission to the user
     */
    // public function grantPermission(Permission $perm)
    // {
    //     if (!$this->permissions->contains($perm->id)) {

    //         return $this->permissions()->attach($perm);

    //     }

    //     return false;
    // }

    /**
     * Revokes a user's permission
     */
    // public function revokePermission(Permission $perm)
    // {
    //     return $this->permissions()->detach($perm);
    // }

    /**
     *  Check if user has a single permission
     *
     *  @param string $permission Permission type.
     *  @return bool
     */
    // public function hasPermission($permission)
    // {

    //     if (is_array($permission)) {

    //         return $this->hasPermissions($permission);

    //     }

    //     return in_array($permission, $this->allPermissions()->toArray());

    // }

    /**
     *  Check if user has a group of permissions
     *
     *  @param array permissions
     *  @return bool
     */
    // public function hasPermissions($permissions)
    // {
    //     if (is_string($permissions)) {
    //         $permissions = explode(",", $permissions);
    //     }

    //     if (count($permissions) == 0) {
    //         return true;
    //     }

    //     return (bool)count(array_intersect($this->allPermissions()->toArray(), $permissions)) == count($permissions);

    // }

}