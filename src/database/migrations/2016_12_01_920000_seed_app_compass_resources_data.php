<?php

use Illuminate\Database\Migrations\Migration;
use P3in\Builders\MenuBuilder;
use P3in\Models\Resource;
use P3in\Models\WebProperty;

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

        $dashboard = Resource::build('cp-dashboard')->setLayout('Private')->setComponent('Home')->setTitle('Dashbaord')->setPermission('cp_login')->requiresAuth();

        $users = Resource::build('users.index')->setLayout('Private')->setComponent('List')->setTitle('Users')->setPermission('users_admin')->requiresAuth();
        $user = Resource::build('users.show')->setLayout('Private')->setComponent('Edit')->setTitle('User')->setPermission('users_admin')->requiresAuth();
        $user_profile = Resource::build('users.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Profile')->setPermission('users_admin')->requiresAuth();
        $create_user = Resource::build('users.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('users_admin')->requiresAuth();

        $user_roles = Resource::build('users.roles.index')->setLayout('Private')->setComponent('List')->setTitle('Roles')->setPermission('users_admin')->requiresAuth();
        $user_permissions = Resource::build('users.permissions.index')->setLayout('Private')->setComponent('List')->setTitle('Permissions')->setPermission('users_admin')->requiresAuth();

        $permissions = Resource::build('permissions.index')->setLayout('Private')->setComponent('List')->setTitle('Permissions')->setPermission('permissions_admin')->requiresAuth();
        $permission = Resource::build('permissions.show')->setLayout('Private')->setComponent('Edit')->setTitle('Permission')->setPermission('permissions_admin')->requiresAuth();
        $permission_info = Resource::build('permissions.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('permissions_admin')->requiresAuth();
        $create_permission = Resource::build('permissions.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('permissions_admin')->requiresAuth();

        $roles = Resource::build('roles.index')->setLayout('Private')->setComponent('List')->setTitle('Roles')->setPermission('permissions_admin')->requiresAuth();
        $role = Resource::build('roles.show')->setLayout('Private')->setComponent('Edit')->setTitle('Role')->setPermission('permissions_admin')->requiresAuth();
        $role_info = Resource::build('roles.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('permissions_admin')->requiresAuth();
        $create_role = Resource::build('roles.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('permissions_admin')->requiresAuth();
        $role_permissions = Resource::build('roles.permissions.index')->setLayout('Private')->setComponent('List')->setTitle('Permissions')->setPermission('permissions_admin')->requiresAuth();

        // @TODO: do we really want to allow admins to edit resources end points via the UI?
        $resources = Resource::build('resources.index')->setLayout('Private')->setComponent('List')->setTitle('Resources')->setPermission('resources_admin')->requiresAuth();
        $resource = Resource::build('resources.show')->setLayout('Private')->setComponent('Edit')->setTitle('Resource')->setPermission('resources_admin')->requiresAuth();
        $resource_info = Resource::build('resources.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('resources_admin')->requiresAuth();
        $create_resource = Resource::build('resources.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('resources_admin')->requiresAuth();

        $forms = Resource::build('forms.index')->setLayout('Private')->setComponent('List')->setTitle('Forms')->setPermission('forms_admin')->requiresAuth();
        $form = Resource::build('forms.show')->setLayout('Private')->setComponent('Edit')->setTitle('Form')->setPermission('forms_admin')->requiresAuth();
        $form_info = Resource::build('forms.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('forms_admin')->requiresAuth();
        $create_form = Resource::build('forms.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('forms_admin')->requiresAuth();

        $user_nav = MenuBuilder::new('user_nav', $cp);
        $user_nav->add(['title' => 'Profile', 'url' => '/users/:current_user_id', 'alt' => 'Profile'], 1)->icon('user');

        $main_nav = MenuBuilder::new('main_nav', $cp);
        $main_nav->add($dashboard, 1)
            ->add(['title' => 'Users Management', 'alt' => 'Users Management'], 2)->sub()
            ->add($users, 1)->icon('user')->sub()
            ->add($user_profile, 1)->icon('user')
            ->add($user_roles, 2)->icon('group')
            ->add($user_permissions, 3)->icon('permission')
            ->parent()
            ->add($roles, 2)->icon('group')->sub()
            ->add($role_info, 1)->icon('edit')
            ->add($role_permissions, 2)->icon('permission')
            ->parent()
            ->add($permissions, 3)->icon('permission')->sub()
            ->add($permission_info, 1)->icon('edit')
            ->parent()
            ->parent()
            ->add(['title' => 'Settings', 'alt' => 'Settings'], 5)->sub()
            ->add($forms, 2)->icon('file')->sub()
            ->add($form_info, 1)->icon('file')
            ->parent()
            ->add($resources, 4)->icon('diamond')->sub()
            ->add($resource_info, 1)->icon('edit');
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
