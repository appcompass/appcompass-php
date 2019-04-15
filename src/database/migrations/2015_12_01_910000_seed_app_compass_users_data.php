<?php

use Illuminate\Database\Migrations\Migration;
use AppCompass\AppCompass\Models\User;
use AppCompass\AppCompass\Models\Permission;
use AppCompass\AppCompass\Models\Role;

class SeedAppCompassUsersData extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $systemRole = Role::create([
            'name' => 'system',
            'label' => 'System',
            'description' => 'System users',
            'active' => true,
        ]);

        Role::create([
            'name' => 'admin',
            'label' => 'Admin',
            'description' => 'Administrators',
            'active' => true,
        ]);

        $userRole = Role::create([
            'name' => 'user',
            'label' => 'User',
            'description' => 'Regular User',
            'active' => true,
        ]);

        Permission::create([
            'name' => Permission::GUEST_PERM_NAME,
            'label' => 'Guest',
            'description' => 'Permission used to allow resources to be viewable only to guest users.',
            'system' => true,
        ]);

        $loggedInPerm = Permission::create([
            'name' => Permission::LOGGED_IN_PERM_NAME,
            'label' => 'User',
            'description' => 'The user can log into the application frontend (websites)',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'cp_login',
            'label' => 'Cp Login',
            'description' => 'A Permission giving it\'s holder the ability to log into the control panel.',
            'system' => true,
        ]);

        $users_admin = Permission::create([
            'name' => 'users_admin',
            'label' => 'Users Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all users in the system.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'all_users_admin',
            'label' => 'All Users Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all users under the system.',
            'system' => true,
        ]);

        $companies_admin = Permission::create([
            'name' => 'companies_admin',
            'label' => 'Companies Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all companies in the system.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'permissions_admin',
            'label' => 'Permissions Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all permissions in the system.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'resources_admin',
            'label' => 'Resources Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all Control Panel available features.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'websites_admin_view',
            'label' => 'Websites Admin View',
            'description' => 'A Permission giving it\'s holder the ability to view all websites from within the control panel.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'websites_admin_create',
            'label' => 'Websites Admin Create',
            'description' => 'A Permission giving it\'s holder the ability to create websites.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'websites_layouts_admin',
            'label' => 'Websites Layouts Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage website layouts.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'websites_menus_admin',
            'label' => 'Websites Menus Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage Website Navigation Menus.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'websites_pages_admin',
            'label' => 'Websites Pages Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage a website\'s pages.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'storage_admin',
            'label' => 'Storage Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage Disk instances used by the websites.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'forms_admin',
            'label' => 'Forms Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage Forms.',
            'system' => true,
        ]);

        Permission::create([
            'name' => 'websites_admin_destroy',
            'label' => 'Websites Admin Destroy',
            'description' => 'A Permission giving it\'s holder the ability to delete a website.',
            'system' => true,
        ]);

        $userRole->setPermission($users_admin);

        $userRole->grantPermissions([
            $loggedInPerm,
        ]);

        $loggedInPerm->setPermission($users_admin);

        User::create([
            'name' => 'System User',
            'email' => config('app-compass.system_user'),
            'password' => str_random(32),
        ])->assignRole($systemRole)
        ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::where([
            'name' => 'System User',
            'email' => config('app-compass.system_user'),
        ])->delete();

        Role::where([
            'name' => 'system',
            'label' => 'System',
            'active' => true,
        ])->delete();

        Role::where([
            'name' => 'admin',
            'label' => 'Admin',
            'active' => true,
        ])->delete();

        Role::where([
            'name' => 'user',
            'label' => 'User',
            'active' => true,
        ])->delete();

        Permission::where([
            'name' => Permission::GUEST_PERM_NAME,
            'label' => 'Guest',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => Permission::LOGGED_IN_PERM_NAME,
            'label' => 'User',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'cp_login',
            'label' => 'Cp Login',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'users_admin',
            'label' => 'Users Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'all_users_admin',
            'label' => 'All Users Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'companies_admin',
            'label' => 'Companies Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'permissions_admin',
            'label' => 'Permissions Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'resources_admin',
            'label' => 'Resources Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'websites_admin_view',
            'label' => 'Websites Admin View',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'websites_admin_create',
            'label' => 'Websites Admin Create',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'websites_layouts_admin',
            'label' => 'Websites Layouts Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'websites_menus_admin',
            'label' => 'Websites Menus Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'websites_pages_admin',
            'label' => 'Websites Pages Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'storage_admin',
            'label' => 'Storage Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'forms_admin',
            'label' => 'Forms Admin',
            'system' => true,
        ])->delete();

        Permission::where([
            'name' => 'websites_admin_destroy',
            'label' => 'Websites Admin Destroy',
            'system' => true,
        ])->delete();
    }
}
