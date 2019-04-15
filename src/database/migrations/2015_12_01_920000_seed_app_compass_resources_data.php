<?php

use Illuminate\Database\Migrations\Migration;
use AppCompass\AppCompass\Builders\MenuBuilder;
use AppCompass\AppCompass\Models\Resource;
use AppCompass\AppCompass\Models\WebProperty;

class SeedAppCompassResourcesData extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cp = new WebProperty([
            'name'   => config('app-compass.admin_site_name'),
            'scheme' => config('app-compass.admin_site_scheme'),
            'host'   => config('app-compass.admin_site_host'),
        ]);

        $cp->save();

        $dashboard = Resource::build('cp-dashboard', $cp)
            ->setLayout('Private')
            ->setComponent('Home')
            ->setTitle('Dashboard')
            ->setPermission('cp_login')
            ->requiresAuth()
        ;

        $users = Resource::build('users.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Users')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;
        $user = Resource::build('users.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('User')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;
        $user_profile = Resource::build('users.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Profile')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;
        $create_user = Resource::build('users.create', $cp)
            ->setLayout('Private')
            ->setComponent('Create')
            ->setTitle('Create')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;

        $user_roles = Resource::build('users.roles.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Roles')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;
        $user_permissions = Resource::build('users.permissions.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Permissions')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;

        $companies = Resource::build('companies.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Companies')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;

        $company = Resource::build('companies.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Company')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_edit = Resource::build('companies.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Company')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_users = Resource::build('companies.users.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Users')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_user = Resource::build('companies.users.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('User')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_user_edit = Resource::build('companies.users.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('User')
            ->setPermission('users_admin')
            ->requiresAuth()
        ;
        $create_company_user = Resource::build('companies.users.create', $cp)
            ->setLayout('Private')
            ->setComponent('Create')
            ->setTitle('Create')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_user_profile = Resource::build('companies.users.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Profile')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_user_roles = Resource::build('companies.users.roles.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Roles')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;
        $company_user_permissions = Resource::build('companies.users.permissions.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Permissions')
            ->setPermission('companies_admin')
            ->requiresAuth()
        ;

        $permissions = Resource::build('permissions.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Permissions')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $permission = Resource::build('permissions.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Permission')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $permission_info = Resource::build('permissions.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Info')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $create_permission = Resource::build('permissions.create', $cp)
            ->setLayout('Private')
            ->setComponent('Create')
            ->setTitle('Create')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;

        $roles = Resource::build('roles.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Roles')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $role = Resource::build('roles.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Role')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $role_info = Resource::build('roles.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Info')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $create_role = Resource::build('roles.create', $cp)
            ->setLayout('Private')
            ->setComponent('Create')
            ->setTitle('Create')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;
        $role_permissions = Resource::build('roles.permissions.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Permissions')
            ->setPermission('permissions_admin')
            ->requiresAuth()
        ;

        // @TODO: do we really want to allow admins to edit resources end points via the UI?
        $resources = Resource::build('resources.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Resources')
            ->setPermission('resources_admin')
            ->requiresAuth()
        ;
        $resource = Resource::build('resources.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Resource')
            ->setPermission('resources_admin')
            ->requiresAuth()
        ;
        $resource_info = Resource::build('resources.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Info')
            ->setPermission('resources_admin')
            ->requiresAuth()
        ;
        $create_resource = Resource::build('resources.create', $cp)
            ->setLayout('Private')
            ->setComponent('Create')
            ->setTitle('Create')
            ->setPermission('resources_admin')
            ->requiresAuth()
        ;

        $forms = Resource::build('forms.index', $cp)
            ->setLayout('Private')
            ->setComponent('List')
            ->setTitle('Forms')
            ->setPermission('forms_admin')
            ->requiresAuth()
        ;
        $form = Resource::build('forms.show', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Form')
            ->setPermission('forms_admin')
            ->requiresAuth()
        ;
        $form_info = Resource::build('forms.edit', $cp)
            ->setLayout('Private')
            ->setComponent('Edit')
            ->setTitle('Info')
            ->setPermission('forms_admin')
            ->requiresAuth()
        ;
        $create_form = Resource::build('forms.create', $cp)
            ->setLayout('Private')
            ->setComponent('Create')
            ->setTitle('Create')
            ->setPermission('forms_admin')
            ->requiresAuth()
        ;

        $user_nav = MenuBuilder::new('user_nav', $cp);
        $user_nav->add(['title' => 'Profile', 'url' => '/users/:current_user_id', 'alt' => 'Profile'], 1)->icon('user');

        $main_nav = MenuBuilder::new('main_nav', $cp);
        $main_nav->add($dashboard, 1)
            ->add(['title' => 'Users Management', 'alt' => 'Users Management'], 2)
            ->sub()
            ->add($users, 1)->icon('user')
            ->sub()
            ->add($user_profile, 1)->icon('user')
            ->add($user_roles, 2)->icon('group')
            ->add($user_permissions, 3)->icon('permission')
            ->add($user_roles, 2)->icon('group')
            ->add($user_permissions, 3)->icon('permission')
            ->parent()
            ->add($companies, 2)->icon('users')
            ->sub()
            ->add($company_edit, 1)->icon('users')
            ->add($company_users, 2)->icon('group')
            ->sub()
            ->add($company_user, 1)->icon('user')
            ->add($company_user_roles, 2)->icon('group')
            ->add($company_user_permissions, 3)->icon('permission')
            ->parent()
            ->parent()
            ->add($roles, 3)->icon('group')
            ->sub()
            ->add($role_info, 1)->icon('edit')
            ->add($role_permissions, 2)->icon('permission')
            ->parent()
            ->add($permissions, 4)->icon('permission')
            ->sub()
            ->add($permission_info, 1)->icon('edit')
            ->parent()
            ->parent()
            ->add(['title' => 'Settings', 'alt' => 'Settings'], 99)
            ->sub()
            ->add($forms, 2)->icon('file')
            ->sub()
            ->add($form_info, 1)->icon('file')
            ->parent()
            ->add($resources, 4)->icon('diamond')
            ->sub()
            ->add($resource_info, 1)->icon('edit')
        ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // @TODO: add deletions for each of the above.
    }
}
