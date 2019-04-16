<?php
namespace AppCompass\AppCompass\Tests;

use AppCompass\AppCompass\Models\User;
use AppCompass\AppCompass\Models\Role;
use AppCompass\AppCompass\Models\Permission;

class UserDataMigrationsTest extends TestCase
{
    private $guest_permission_details = [
        'name' => Permission::GUEST_PERM_NAME,
        'label' => 'Guest',
        'description' => 'Permission used to allow resources to be viewable only to guest users.',
        'system' => true,
    ];

    private $logged_in_permission_details = [
        'name' => Permission::LOGGED_IN_PERM_NAME,
        'label' => 'User',
        'description' => 'The user can log into the application frontend (websites)',
        'system' => true,
    ];

    private $user_admin_permission_details = [
        'name' => 'users_admin',
        'label' => 'Users Admin',
        'description' => 'A Permission giving it\'s holder the ability to manage all users in the system.',
        'system' => true,
    ];

    public function testSystemRoleAndUser()
    {
        $system_role = Role::where([
            'name' => 'system',
            'label' => 'System',
            'description' => 'System users',
            'active' => true,
        ])->first();

        $system_user = User::where([
            'name' => 'System User',
            'email' => config('app-compass.system_user'),
        ])->first();

        $this->assertNotFalse($system_role);
        $this->assertNotFalse($system_user);
        $this->assertContains($system_user->roles->toArray(), $system_role->toArray());
    }

    public function testAdminUserDataMigration()
    {
        $admin_role = Role::where([
            'name' => 'admin',
            'label' => 'Admin',
            'description' => 'Administrators',
            'active' => true,
        ])->first();

        $this->assertNotFalse($admin_role);
    }

    public function testUserRoleDataMigration()
    {
        $user_role = Role::where([
            'name' => 'user',
            'label' => 'User',
            'description' => 'Regular User',
            'active' => true,
        ])->first();
        $users_admin_permission = Permission::where($this->user_admin_permission_details)->first();
        $logged_in_permission = Permission::where($this->logged_in_permission_details)->first();

        $this->assertNotFalse($user_role);
        $this->assertEquals($user_role->assignable_by->toArray(), $users_admin_permission->toArray());
        $this->assertContains($user_role->permissions->toArray(), $logged_in_permission->toArray());
    }

    public function testGuestPermissionDataMigration()
    {
        $guest_permission = Permission::where($this->guest_permission_details)->first();

        $this->assertNotFalse($guest_permission);
    }

    public function testLoggedInPermissionDataMigration()
    {
        $users_admin_permission = Permission::where($this->user_admin_permission_details)->first();
        $logged_in_permission = Permission::where($this->logged_in_permission_details)->first();

        $this->assertNotFalse($logged_in_permission);
        $this->assertEquals(
            $logged_in_permission->assignable_by->toArray(),
            $users_admin_permission->toArray()
        );
    }

    public function testCpLoginPermissionDataMigration()
    {
        $cp_login_permission = Permission::where([
            'name' => 'cp_login',
            'label' => 'Cp Login',
            'description' => 'A Permission giving it\'s holder the ability to log into the control panel.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($cp_login_permission);
    }

    public function testUsersAdminPermissionDataMigration()
    {
        $users_admin_permission = Permission::where($this->user_admin_permission_details)->first();

        $this->assertNotFalse($users_admin_permission);
    }

    public function testAllUsersAdminPermissionDataMigration()
    {
        $all_users_admin_permission = Permission::where([
            'name' => 'all_users_admin',
            'label' => 'All Users Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all users under the system.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($all_users_admin_permission);
    }

    public function testCompaniesAdminPermissionDataMigration()
    {
        $companies_admin_permission = Permission::where([
            'name' => 'companies_admin',
            'label' => 'Companies Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all companies in the system.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($companies_admin_permission);
    }

    public function testPermissionsAdminPermissionDataMigration()
    {
        $permissions_admin_permission = Permission::where([
            'name' => 'permissions_admin',
            'label' => 'Permissions Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all permissions in the system.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($permissions_admin_permission);
    }

    public function testResourcesAdminPermissionDataMigration()
    {
        $resources_admin_permission = Permission::where([
            'name' => 'resources_admin',
            'label' => 'Resources Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage all Control Panel available features.',
            'system' => true,
            ])->first();

        $this->assertNotFalse($resources_admin_permission);
    }

    public function testWebsitesAdminViewPermissionDataMigration()
    {
        $websites_admin_view_permission = Permission::where([
            'name' => 'websites_admin_view',
            'label' => 'Websites Admin View',
            'description' => 'A Permission giving it\'s holder the ability to view all websites from within the control panel.',
            'system' => true,
            ])->first();

        $this->assertNotFalse($websites_admin_view_permission);
    }

    public function testWebsitesAdminCreatePermissionDataMigration()
    {
        $websites_admin_create_permission = Permission::where([
            'name' => 'websites_admin_create',
            'label' => 'Websites Admin Create',
            'description' => 'A Permission giving it\'s holder the ability to create websites.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($websites_admin_create_permission);
    }

    public function testWebsitesLayoutsAdminPermissionDataMigration()
    {
        $websites_layouts_admin_permission = Permission::where([
            'name' => 'websites_layouts_admin',
            'label' => 'Websites Layouts Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage website layouts.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($websites_layouts_admin_permission);
    }

    public function testWebsitesMenusAdminPermissionDataMigration()
    {
        $websites_menus_admin_permission = Permission::where([
            'name' => 'websites_menus_admin',
            'label' => 'Websites Menus Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage Website Navigation Menus.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($websites_menus_admin_permission);
    }

    public function testWebsitesPagesAdminPermissionDataMigration()
    {
        $websites_pages_admin_permission = Permission::where([
            'name' => 'websites_pages_admin',
            'label' => 'Websites Pages Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage a website\'s pages.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($websites_pages_admin_permission);
    }

    public function testStorageAdminPermissionDataMigration()
    {
        $storage_admin_permission = Permission::where([
            'name' => 'storage_admin',
            'label' => 'Storage Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage Disk instances used by the websites.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($storage_admin_permission);
    }

    public function testFormsAdminPermissionDataMigration()
    {
        $forms_admin_permission = Permission::where([
            'name' => 'forms_admin',
            'label' => 'Forms Admin',
            'description' => 'A Permission giving it\'s holder the ability to manage Forms.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($forms_admin_permission);
    }

    public function testWebsitesAdminDestroyPermissionDataMigration()
    {
        $websites_admin_destroy_permission = Permission::where([
            'name' => 'websites_admin_destroy',
            'label' => 'Websites Admin Destroy',
            'description' => 'A Permission giving it\'s holder the ability to delete a website.',
            'system' => true,
        ])->first();

        $this->assertNotFalse($websites_admin_destroy_permission);
    }
}
