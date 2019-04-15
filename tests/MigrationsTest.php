<?php
namespace AppCompass\AppCompass\Tests;

use Illuminate\Support\Facades\Schema;

class MigrationsTest extends TestCase
{
    public function testMigrations()
    {
        $web_properties = Schema::getColumnListing('web_properties');
        $this->assertEquals([
            'id',
            'name',
            'scheme',
            'host',
            'created_at',
            'updated_at',
            'deleted_at'
        ], $web_properties);

        $users = Schema::getColumnListing('users');
        $this->assertEquals([
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'active',
            'activation_code',
            'activated_at',
            'last_login'
        ], $users);

        $companies = Schema::getColumnListing('companies');
        $this->assertEquals([
            'id',
            'name',
            'created_at',
            'updated_at'
        ], $companies);

        $roles = Schema::getColumnListing('roles');
        $this->assertEquals([
            'id',
            'name',
            'label',
            'description',
            'active',
            'created_at',
            'updated_at',
            'assignable_by_id'
        ], $roles);

        $permissions = Schema::getColumnListing('permissions');
        $this->assertEquals([
            'id',
            'name',
            'label',
            'description',
            'assignable_by_id',
            'system',
            'created_at',
            'updated_at'
        ], $permissions);

        $resources = Schema::getColumnListing('resources');
        $this->assertEquals([
            'id',
            'name',
            'config',
            'web_property_id',
            'form_id',
            'req_perm',
            'created_at',
            'updated_at'
        ], $resources);

        $form_storage = Schema::getColumnListing('form_storage');
        $this->assertEquals([
            'id',
            'form_id',
            'content',
            'created_at',
            'updated_at'
        ], $form_storage);

        $menus = Schema::getColumnListing('menus');
        $this->assertEquals([
            'id',
            'web_property_id',
            'name',
            'created_at',
            'updated_at'
        ], $menus);

        $links = Schema::getColumnListing('links');
        $this->assertEquals([
            'id',
            'title',
            'alt',
            'new_tab',
            'url',
            'clickable',
            'icon',
            'content',
            'req_perm',
            'web_property_id',
            'created_at',
            'updated_at'
        ], $links);

        $menu_items = Schema::getColumnListing('menu_items');
        $this->assertEquals([
            'id',
            'menu_id',
            'parent_id',
            'title',
            'alt',
            'new_tab',
            'url',
            'order',
            'req_perm',
            'clickable',
            'icon',
            'sort',
            'navigatable_type',
            'navigatable_id',
            'created_at',
            'updated_at'
        ], $menu_items);

        $failed_jobs = Schema::getColumnListing('failed_jobs');
        $this->assertEquals([
            'id',
            'connection',
            'queue',
            'payload',
            'exception',
            'failed_at'
        ], $failed_jobs);

        $notifications = Schema::getColumnListing('notifications');
        $this->assertEquals([
            'id',
            'type',
            'notifiable_type',
            'notifiable_id',
            'data',
            'read_at',
            'created_at',
            'updated_at'
        ], $notifications);

    }
}
