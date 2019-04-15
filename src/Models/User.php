<?php

namespace AppCompass\AppCompass\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use AppCompass\AppCompass\Notifications\ConfirmRegistration;
use AppCompass\AppCompass\Notifications\ResetPassword;
use AppCompass\AppCompass\Traits\HasCardView;
use AppCompass\AppCompass\Traits\HasPermissions;
use AppCompass\AppCompass\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Company;

class User extends Authenticatable implements JWTSubject
    // AuthenticatableContract,
    // AuthorizableContract,
    // CanResetPasswordContract
{

    use Notifiable,
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'active', 'activation_code',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Stuff to append to each request
     *
     *
     */
    protected $appends = ['gravatar_url'];

    public static $rules = [
        'name' => 'required|max:255',
        'email'      => 'required|email|max:255', //|unique:users when registrering only
        'password'   => 'min:6|confirmed', //|required when registering only.
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class)->withPivot('current');
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

    public function scopeSystem(Builder $query)
    {
        $query->where('email', config('app-compass.system_user'));
    }

    public function assignCompanies($companies)
    {
        foreach ($companies as $company) {
            $this->assignCompany($company);
        }
    }

    public function assignCompany($company)
    {
        switch (true) {
            case $company instanceof Company:
                break;
            case is_int($company):
                $company = Company::findOrFail($company);
                break;
            case is_string($company):
            default:
                $company = Company::whereName($company)->firstOrFail();
                break;
        }

        if (!$this->belongsToCompany($company)) {
            $this->companies()->attach($company);
        }
        return $this;

    }

    public function belongsToCompany($company)
    {
        return $this->where('id', $this->id) // @TODO: seems to be a bug in Laravel?  report it and see what comes of it.
        ->whereHas('companies', function ($query) use ($company) {
            $field = 'id';
            $value = null;
            if ($company instanceof Company) {
                $field = 'id';
                $value = $company->id;
            } elseif (is_string($company)) {
                $field = 'name';
                $value = $company;
            } elseif (is_int($company)) {
                $field = 'id';
                $value = $company;
            }
            $query->where($field, $value);
        })->exists()
            ;
    }

    public function setCompany($company_id)
    {
        $this->companies()
            ->newPivotStatement()
            ->where('company_id', $company_id)
            ->where('user_id', $this->id)
            ->update(['current' => true]);

        $this->companies()
            ->newPivotStatement()
            ->where('company_id', '!=', $company_id)
            ->where('user_id', $this->id)
            ->update(['current' => false]);

        return $this;
    }

    /**
     *  Get user's full name
     *
     */
    public function getFullNameAttribute()
    {
        return sprintf("%s %s", $this->first_name, $this->last_name);
    }

    public function getCurrentCompanyAttribute()
    {
        if (!is_array($this->companies)) {
            $current = $this->companies->where('pivot.current', true)->first();
            if (!$current) {
                $current = $this->companies->first();
                if ($current) {
                    $this->setCompany($current->id);
                }
            }

            return $current;
        }
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

    public function setCompaniesAttribute($value)
    {
        if (is_array($value) && $this->exists) {
            $collection = collect($value);
            $ids = $collection->pluck('id')->all();
            $this->companies()->sync($ids);
        }
    }

    /**
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function allPermissions($pluck = 'id')
    {
        // $this->load('roles.permissions');

        // get User specific permissions.
        $all_permissions = $this->permissions()->whereNull('permission_user.company_id')->pluck($pluck);
        $user_roles = $this->roles()->whereNull('role_user.company_id')->with('permissions')->get();

        foreach ($user_roles as $role) {
            $all_permissions = $all_permissions->merge($role->permissions->pluck($pluck));
        }

        // get Company User specific permissions
        if ($this->current_company) {
            $company_user_permissions = $this->permissions()
                ->where('permission_user.company_id', $this->current_company->id)->pluck($pluck);
            $company_user_roles = $this->roles()
                ->where('role_user.company_id', $this->current_company->id)
                ->with('permissions')
                ->get();

            foreach ($company_user_roles as $role) {
                $company_user_permissions = $company_user_permissions->merge($role->permissions->pluck($pluck));
            }

            $all_permissions = $all_permissions->merge($company_user_permissions);

        }

        return $all_permissions->unique()->values()->all();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     *
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
