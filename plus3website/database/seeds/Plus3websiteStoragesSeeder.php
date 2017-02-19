<?php

namespace P3in\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use P3in\Models\StorageType;


class Plus3websiteStoragesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $local = StorageType::getType('local');

        $local->createDrive('plus3website', [
            'driver' => 'local',
            'root' => base_path('../websites/plus3website'),
        ]);

        $local->createDrive('plus3website_images', [
            'driver' => 'local',
            'root' => base_path('../websites/plus3website/static/assets/images/content'),
        ]);

    }
}