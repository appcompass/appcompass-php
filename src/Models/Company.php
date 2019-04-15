<?php

namespace AppCompass\AppCompass\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use AppCompass\AppCompass\Traits\HasPermissions;
use AppCompass\AppCompass\Traits\HasRoles;

class Company extends Model
{

    use
        // Notifiable,
        HasPermissions,
        HasRoles // , HasProfileTrait
        ;

    protected $table = 'companies';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Stuff to append to each request
     *
     *
     */
    protected $appends = [];

    public static $rules = [
        'name' => 'required|max:255',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    /**
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function allPermissions()
    {
        $company_permissions = $this->permissions()->allRelatedIds();
        $company_roles = $this->roles->load('permissions');

        $all_permissions = collect($company_permissions);

        foreach ($company_roles as $role) {
            $role_permissions = $role->permissions->pluck('id');

            $all_permissions = $all_permissions->merge($role_permissions);
        }

        return $all_permissions->unique()->values()->all();
    }

}
