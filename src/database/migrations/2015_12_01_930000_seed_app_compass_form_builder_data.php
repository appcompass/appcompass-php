<?php

use Illuminate\Database\Migrations\Migration;
use P3in\Builders\FormBuilder;
use P3in\Models\FieldSource;
use P3in\Models\Permission;
use P3in\Models\Resource;
use P3in\Models\WebProperty;
use App\Company;

class SeedAppCompassFormBuilderData extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cp = WebProperty::where([
            'name'   => config('app-compass.admin_site_name'),
            'scheme' => config('app-compass.admin_site_scheme'),
            'host'   => config('app-compass.admin_site_host'),
        ])->firstOrFail();

        $companiesForm = FormBuilder::new('companies', function (FormBuilder $builder) {
            $builder->string('Customer Number', 'customer_number')->list()->edit(false)->sortable()->searchable();
            $builder->string('Name', 'name')->list()->edit(false)->sortable()->searchable();
            $builder->string('User Count', 'users_count')->list()->edit(false)->sortable()->searchable();
        })->getForm();

        Resource::buildAll([
            'companies.index',
            'companies.show',
            'companies.create',
            'companies.update',
            'companies.store',
        ], $cp, $companiesForm, 'users_admin');

        $users = FormBuilder::new('users', function (FormBuilder $builder) {
            $builder->string('First Name', 'first_name')->list()->validation(['required', 'max:255'])->sortable()->searchable();
            $builder->string('Last Name', 'last_name')->list()->validation(['required', 'max:255'])->sortable()->searchable();
            $builder->string('Email', 'email')->list()->validation(['required', 'email', 'unique:users,email', 'max:255'])->sortable()->searchable();
            $builder->string('Phone Number', 'phone')->list()->validation(['phone:AUTO,US'])->sortable()->searchable();
            $builder->boolean('Active', 'active')->validation(['nullable'])->list()->sortable();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Last Login', 'last_login')->list()->edit(false)->sortable()->searchable();
            $builder->password('Password', 'password')->validation(['min:6', 'confirmed']);

            $builder->dropdownSearch('Associated Companies', 'companies')->dynamic(Company::class,
                function (FieldSource $source) {
                    $source->select(['id AS index', 'name AS label']);
                })->list(false)->edit()->required()->multiple(true);

            $builder->checkboxes('Companies', 'companies')
                ->list()
                ->edit(false)
                ->sortable(false)
                ->searchable(false);

        })->getForm();

        Resource::buildAll([
            'users.index',
            'users.show',
            'users.update',
            'companies.users.index',
            'companies.users.show',
            'companies.users.update',
        ], $cp, $users, 'users_admin');

        $newUsers = FormBuilder::new('users_create', function (FormBuilder $builder) {
            $builder->string('First Name', 'first_name')->validation(['required', 'max:255'])->sortable()->searchable();
            $builder->string('Last Name', 'last_name')->validation(['required', 'max:255'])->sortable()->searchable();
            $builder->string('Email', 'email')->validation(['required', 'email', 'unique:users,email', 'max:255'])->sortable()->searchable();
            $builder->string('Phone Number', 'phone')->validation(['phone:AUTO,US'])->sortable()->searchable();
            $builder->boolean('Active', 'active')->validation(['nullable'])->sortable();
            $builder->password('Password', 'password')->validation(['required', 'min:6', 'confirmed']);

        })->getForm();

        Resource::buildAll([
            'users.create',
            'users.store',
            'companies.users.create',
            'companies.users.store',
        ], $cp, $newUsers, 'users_admin');

        $userRoles = FormBuilder::new('user-roles', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
            $builder->string('Description', 'description')->list()->required()->sortable()->searchable();
        })->getForm();

        Resource::buildAll([
            'users.roles.index',
        ], $cp, $userRoles, 'users_admin');

        $userPermissions = FormBuilder::new('user-permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
            $builder->string('Description', 'description')->list()->required()->sortable()->searchable();
        })->getForm();

        Resource::buildAll([
            'users.permissions.index',
        ], $cp, $userPermissions, 'users_admin');

        $permissions = FormBuilder::new('permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
            $builder->text('Description', 'description')->list(false)->required()->sortable()->searchable();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();

            $builder->select('Assignable By', 'assignable_by_id')
                ->dynamic(Permission::class, function (FieldSource $source) {
                    $source->select(['id AS index', 'name AS label']);
                })->list(false);
        })->getForm();

        Resource::buildAll([
            'permissions.index',
            'permissions.show',
            'permissions.create',
            'permissions.store',
            'permissions.update',
        ], $cp, $permissions, 'permissions_admin');

        $roles = FormBuilder::new('roles', function (FormBuilder $builder) {
            $builder->string('Role Name', 'name')->list()->required()->sortable()->searchable();
            $builder->string('Role Label', 'label')->list()->required()->sortable()->searchable();
            $builder->text('Description', 'description')->list(false)->required()->sortable()->searchable();
            $builder->select('Assignable By', 'assignable_by_id')
                ->dynamic(Permission::class, function (FieldSource $source) {
                    $source->select(['id AS index', 'name AS label']);
                })->list(false);
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
        })->getForm();

        Resource::buildAll([
            'roles.index',
            'roles.show',
            'roles.create',
            'roles.store',
            'roles.update',
        ], $cp, $roles, 'permissions_admin');

        $rolePermissions = FormBuilder::new('role-permissions', function (FormBuilder $builder) {
            $builder->string('Name', 'label')->list()->required()->sortable()->searchable();
        })->getForm();

        Resource::buildAll([
            'roles.permissions.index',
        ], $cp, $rolePermissions, 'permissions_admin');

        $resources = FormBuilder::new('resources', function (FormBuilder $builder) {
            $builder->string('Resource', 'resource')->list()->sortable()->searchable()->required();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
            $builder->select('Role required', 'req_role')->dynamic(\P3in\Models\Role::class,
                function (FieldSource $source) {
                    $source->select(['id As index', 'label']);
                })->nullable();
        })->getForm();

        Resource::buildAll([
            'resources.index',
            'resources.show',
            'resources.create',
        ], $cp, $resources, 'resources_admin');

        $forms = FormBuilder::new('forms', function (FormBuilder $builder) {
            $builder->string('Name', 'name')->list(true)->sortable()->searchable();
            $builder->string('Editor', 'editor');
            $builder->string('Fields', 'fieldsCount')->edit(false)->list();
            $builder->string('Created', 'created_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->list()->edit(false)->sortable()->searchable();
            $builder->string('Updated', 'updated_at')->edit(false);
        })->getForm();
        Resource::buildAll([
            'forms.index',
            'forms.show',
            'forms.create',
        ], $cp, $forms, 'forms_admin');
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
