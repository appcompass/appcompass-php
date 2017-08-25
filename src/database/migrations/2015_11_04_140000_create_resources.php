<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResources extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->json('config')->nullable();
            $table->integer('web_property_id')->unsigned();
            $table->foreign('web_property_id')->references('id')->on('web_properties')->onDelete('cascade');
            $table->integer('form_id')->unsigned()->nullable();
            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
            $table->integer('req_perm')->unsigned()->nullable();
            $table->foreign('req_perm')->references('id')->on('permissions')->onDelete('set null');
            $table->timestamps();

            $table->index('name');
            $table->index('form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('resources');
    }
}
