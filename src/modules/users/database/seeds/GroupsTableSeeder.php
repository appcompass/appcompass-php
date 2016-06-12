<?php

namespace P3in\Seeders;

use Illuminate\Database\Seeder;
use P3in\Models\Group;
use P3in\Models\Permission;
use Modular;
use DB;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //
        //  CP ADMINISTRATOR
        //
        $cp_manager = Group::firstOrCreate([
            'name' => 'cp-admin',
            'label' => 'Control Panel Administrator',
            'description' => "User is allowed to do everything (super-user)",
            'active' => true
        ]);


        //
        //  USER
        //
        $group = Group::firstOrCreate([
            'name' => 'users',
            'label' => 'Users',
            'description' => 'Generic user group',
            'active' => true
        ]);

        if (Modular::isLoaded('permissions')) {
            $cp_manager->grantPermissions(['alert.info']); // something else instead of perms, or make cp-admin a perm not a group.
            $group->grantPermissions(['logged-user', 'alert.info']);
        }
    }
}
