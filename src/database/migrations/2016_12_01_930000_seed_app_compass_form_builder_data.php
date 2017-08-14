<?php

use Illuminate\Database\Migrations\Migration;
use P3in\Builders\FormBuilder;
use P3in\Models\FieldSource;
use P3in\Models\Permission;

class SeedAppCompassFormBuilderData extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FormBuilder::new('users', function (FormBuilder $builder) {
            $builder->string('First Name', 'first_name')->list()->required()->sortable()->searchable();
            $builder->string('Last Name', 'last_name')->list()->required()->sortable()->searchable();
            $builder->string('Email', 'email')->list()->validation(['required', 'email'])->sortable()->searchable();
            $builder->string('Phone Number', 'phone')->list()->sortable()->searchable();
            $builder->boolean('Active', 'active')->list()->sortable();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Last Login', 'last_login')->list()->edit(false)->sortable()->searchable();
            $builder->secret('Password', 'password'); // ->required()
        })->linkToResources([
            'users.index',
            'users.show',
            'users.create',
            'users.update',
            'users.store',
        ], 'users_admin');

        FormBuilder::new('user-roles', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
            $builder->string('Description', 'description')->list()->required()->sortable()->searchable();
        })->linkToResources([
            'users.roles.index',
        ], 'users_admin');

        FormBuilder::new('user-permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
            $builder->string('Description', 'description')->list()->required()->sortable()->searchable();
        })->linkToResources(['users.permissions.index'], 'users_admin');

        FormBuilder::new('permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
            $builder->text('Description', 'description')->list(false)->required()->sortable()->searchable();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();

            $builder->select('Assignable By', 'assignable_by_id')
                ->dynamic(Permission::class, function (FieldSource $source) {
                    $source->select(['id AS index', 'name AS label']);
                })->list(false);
        })->linkToResources([
            'permissions.index',
            'permissions.show',
            'permissions.create',
            'permissions.store',
            'permissions.update',
        ], 'permissions_admin');

        FormBuilder::new('roles', function (FormBuilder $builder) {
            $builder->string('Role Name', 'name')->list()->required()->sortable()->searchable();
            $builder->string('Role Label', 'label')->list()->required()->sortable()->searchable();
            $builder->text('Description', 'description')->list(false)->required()->sortable()->searchable();
            $builder->select('Assignable By', 'assignable_by_id')
                ->dynamic(Permission::class, function (FieldSource $source) {
                    $source->select(['id AS index', 'name AS label']);
                })->list(false);
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
        })->linkToResources([
            'roles.index',
            'roles.show',
            'roles.store',
            'roles.update',
        ], 'permissions_admin');

        FormBuilder::new('role-permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
        })->linkToResource('roles.permissions.index', 'permissions_admin');

        FormBuilder::new('resources', function (FormBuilder $builder) {
            $builder->string('Resource', 'resource')->list()->sortable()->searchable()->required();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
            $builder->select('Role required', 'req_role')->dynamic(\P3in\Models\Role::class,
                function (FieldSource $source) {
                    $source->select(['id As index', 'label']);
                })->nullable();
        })->linkToResources([
            'resources.index',
            'resources.show',
            'resources.create',
        ], 'resources_admin');

        FormBuilder::new('forms', function (FormBuilder $builder) {
            $builder->string('Name', 'name')->list(true)->sortable()->searchable();
            $builder->string('Editor', 'editor');
            $builder->string('Fields', 'fieldsCount')->edit(false)->list();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->edit(false);
        })->linkToResources([
            'forms.index',
            'forms.show',
        ], 'forms_admin');
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
