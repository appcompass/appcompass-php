<?php

namespace P3in\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use P3in\Models\Navmenu;
use P3in\Models\Page;
use P3in\Models\Website;

class PagesModuleDatabaseSeeder extends Seeder
{

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		Page::where('name','cp_pages_info')->delete();

		$page = Page::firstOrCreate([
		    'name' => 'cp_pages_info',
		    'title' => 'Page Info',
		    'description' => 'Page Info',
		    'slug' => 'edit',
		    'order' => 2,
		    'published_at' => Carbon::now(),
		    'active' => true,
		    'parent' => null,
		    'req_permission' => null,
		    'website_id' => Website::admin()->id
		]);

		Navmenu::byName('cp_pages_subnav')->addItem($page, 2);

		Model::reguard();
	}
}
