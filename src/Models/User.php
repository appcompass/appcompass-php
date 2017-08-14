<?php

namespace P3in\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

use P3in\ModularBaseModel;
use P3in\Notifications\ConfirmRegistration;
use P3in\Notifications\ResetPassword;
use P3in\Traits\HasCardView;
use P3in\Traits\HasPermissions;
use P3in\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;

// use P3in\Traits\HasProfileTrait;

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
        HasCardView,
        HasPermissions,
        HasRoles // , HasProfileTrait
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
        'activation_code',
    ];

    /**
     *  The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activated_at',
        'activation_code',
    ];

    /**
     * Stuff to append to each request
     *
     *
     */
    protected $appends = ['full_name', 'gravatar_url'];

    public static $rules = [
        'first_name' => 'required|max:255',
        'last_name'  => 'required|max:255',
        'email'      => 'required|email|max:255', //|unique:users when registrering only
        'phone'      => 'min:10|max:255',
        // 'password'   => 'min:6|confirmed', //|required when registering only.
    ];


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
        return "https://www.gravatar.com/avatar/" . md5($this->email) . '?d=identicon&s=500';
    }

    public function getCardPhotoUrl()
    {
        return $this->gravatar_url;
    }

    /**
     * Gets the jwt identifier.
     *
     * @return     <type>  The jwt identifier.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Gets the jwt custom claims.
     *
     * @return     array  The jwt custom claims.
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id'   => $this->id,
                'name' => $this->full_name,
            ],
        ];
    }

    /**
     *  Set user's password
     *
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     *  Set user's password
     *
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function allPermissions()
    {
        $this->load('roles.permissions');

        $user_permissions = $this->permissions()->allRelatedIds()->toArray();

        $roles_permissions = [];

        foreach ($this->roles as $role) {
            $role_permissions = $role->permissions()
                ->allRelatedIds()
                ->toArray();

            $roles_permissions = array_merge($roles_permissions, $role_permissions);
        }

        $roles_permissions = array_merge($roles_permissions, $user_permissions);

        return array_unique($roles_permissions);
    }

    /**
     *  Get either all or a specific profile type of a user
     *
     *
     *  TODO: this needs to be refactored a little
     *   -- in pause until we get there
     */
    // public function linkProfile(Model $model)
    // {
    //     $profile = $this->profiles()->firstOrNew([
    //         'profileable_id' => $model->getKey(),
    //         'profileable_type' => get_class($model),
    //     ]);
    //     $profile->save();
    // }

    /**
     * { function_description }
     *
     * @param      <type>  $model_name  The model name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    // @TODO on a break until we get there
    // public function profile($model_name)
    // {
    //     $base_profile = $this->profiles()->where('profileable_type', $model_name)->first();

    //     return $base_profile ? $base_profile->profileable : null;
    // }

    /**
     * { function_description }
     *
     * @param      <type>  $field_name  The field name
     *
     * @return     array   ( description_of_the_return_value )
     */
    // @TODO this is not being called from codebase
    // public function populateField($field_name)
    // {
    //     switch ($field_name) {
    //         case 'users_list':
    //             $users = User::select(\DB::raw("concat(first_name,' ',last_name) as name"), 'id')->get();

    //             return $users->pluck('name', 'id');
    //             break;
    //         default:
    //             return [];
    //             break;
    //     }
    // }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendRegistrationConfirmationNotification()
    {
        $this->notify(new ConfirmRegistration($this->activation_code));
    }
}
