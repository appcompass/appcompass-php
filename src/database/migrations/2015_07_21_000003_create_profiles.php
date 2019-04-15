<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // @TODO move that in when we're actually using them
        // Schema::create('profiles', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('name');
        //     $table->string('class_name')->unique();

        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::drop('profiles');
    }
}
