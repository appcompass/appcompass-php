<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinks extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('alt');
            $table->boolean('new_tab')->default(false);
            $table->string('url', 2083)->nullable(); // null for separators
            $table->boolean('clickable')->default(true); // sometimes we just want separators
            $table->string('icon')->nullable();
            $table->text('content')->nullable();
            $table->integer('req_perm')->unsigned()->nullable();
            $table->foreign('req_perm')->references('id')->on('permissions')->onDelete('set null');
            $table->integer('web_property_id')->unsigned();
            $table->foreign('web_property_id')->references('id')->on('web_properties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('links');
    }
}
