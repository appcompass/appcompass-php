<?php

namespace P3in\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Database\Eloquent\Model;
use P3in\Notifications\ConfirmRegistration;
use P3in\Notifications\ResetPassword;
use P3in\Traits\HasCardView;
use P3in\Traits\HasPermissions;
use P3in\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use App\Company;

class User extends Model implements
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
        'phone'      => 'required', // @TODO: add better support for 'phone:AUTO,US',
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

    public function getPhoneAttribute($value)
    {
        $country = $this->country ?? 'US';
        try {
            $lib = PhoneNumberUtil::getInstance();

            $phoneNumber = PhoneNumber::make($value, $country);

            $phoneNumberInstance = $phoneNumber->getPhoneNumberInstance();

            if ($lib->isValidNumber($phoneNumberInstance)) {
                return $phoneNumber->formatInternational();
            }
        } catch (NumberParseException $e) {
            return $value;
        }
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

    /**
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function allPermissions()
    {
        // $this->load('roles.permissions');


        $user_permissions = $this->permissions()->allRelatedIds();
        $user_roles = $this->roles->load('permissions');

        $all_permissions = collect($user_permissions);

        foreach ($user_roles as $role) {
            $role_permissions = $role->permissions->pluck('id');

            $all_permissions = $all_permissions->merge($role_permissions);
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
