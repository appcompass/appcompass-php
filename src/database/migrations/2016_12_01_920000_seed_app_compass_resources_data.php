<?php

use Illuminate\Database\Migrations\Migration;
use P3in\Models\Resource;

class SeedAppCompassResourcesData extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Resource::build('cp-dashboard')->setLayout('Private')->setComponent('Home')->setTitle('Dashbaord')->setPermission('cp_login')->requiresAuth();

        Resource::build('users.index')->setLayout('Private')->setComponent('List')->setTitle('Users')->setPermission('users_admin')->requiresAuth();
        Resource::build('users.show')->setLayout('Private')->setComponent('Edit')->setTitle('User')->setPermission('users_admin')->requiresAuth();
        Resource::build('users.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Profile')->setPermission('users_admin')->requiresAuth();
        Resource::build('users.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('users_admin')->requiresAuth();

        Resource::build('users.roles.index')->setLayout('Private')->setComponent('List')->setTitle('Roles')->setPermission('users_admin')->requiresAuth();
        Resource::build('users.permissions.index')->setLayout('Private')->setComponent('List')->setTitle('Permissions')->setPermission('users_admin')->requiresAuth();

        Resource::build('permissions.index')->setLayout('Private')->setComponent('List')->setTitle('Permissions')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('permissions.show')->setLayout('Private')->setComponent('Edit')->setTitle('Permission')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('permissions.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('permissions.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('permissions_admin')->requiresAuth();

        Resource::build('roles.index')->setLayout('Private')->setComponent('List')->setTitle('Roles')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('roles.show')->setLayout('Private')->setComponent('Edit')->setTitle('Role')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('roles.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('roles.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('permissions_admin')->requiresAuth();
        Resource::build('roles.permissions.index')->setLayout('Private')->setComponent('List')->setTitle('Permissions')->setPermission('permissions_admin')->requiresAuth();

        // @TODO: do we really want to allow admins to edit resources end points via the UI?
        Resource::build('resources.index')->setLayout('Private')->setComponent('List')->setTitle('Resources')->setPermission('resources_admin')->requiresAuth();
        Resource::build('resources.show')->setLayout('Private')->setComponent('Edit')->setTitle('Resource')->setPermission('resources_admin')->requiresAuth();
        Resource::build('resources.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('resources_admin')->requiresAuth();
        Resource::build('resources.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('resources_admin')->requiresAuth();

        Resource::build('forms.index')->setLayout('Private')->setComponent('List')->setTitle('Forms')->setPermission('forms_admin')->requiresAuth();
        Resource::build('forms.create')->setLayout('Private')->setComponent('Create')->setTitle('Create')->setPermission('forms_admin')->requiresAuth();
        Resource::build('forms.show')->setLayout('Private')->setComponent('Edit')->setTitle('Form')->setPermission('forms_admin')->requiresAuth();
        Resource::build('forms.edit')->setLayout('Private')->setComponent('Edit')->setTitle('Info')->setPermission('forms_admin')->requiresAuth();
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
