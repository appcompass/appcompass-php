<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItems extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('menu_id')->unsigned()->nullable();
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->string('title');
            $table->text('alt');
            $table->boolean('new_tab')->default(false);
            $table->string('url', 2083)->nullable();
            $table->integer('order')->unsigned()->nullable();
            $table->integer('req_perm')->unsigned()->nullable();
            $table->foreign('req_perm')->references('id')->on('permissions')->onDelete('set null');
            $table->boolean('clickable')->default(true); // sometimes we just want separators
            $table->string('icon')->nullable();
            $table->integer('sort')->nullable();

            $table->nullableMorphs('navigatable');

            $table->timestamps();

            $table->index('menu_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('menu_items');
    }
}
