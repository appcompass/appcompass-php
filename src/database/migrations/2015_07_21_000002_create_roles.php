<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('label');
            $table->text('description');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        # link roles to user
        # naming convention: singular table name, alphabetical order, underscore to link
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('role_id')->unsigned()->index();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // $table->unique(['role_id', 'user_id', 'company_id']);
            $table->timestamps();
        });

        DB::connection()
          ->getPdo()
          ->exec('CREATE UNIQUE INDEX permission_user_role_id_user_id_company_id_unique ON role_user (role_id, user_id, company_id) WHERE company_id IS NOT NULL');
        DB::connection()
          ->getPdo()
          ->exec('CREATE UNIQUE INDEX role_user_role_id_user_id_unique ON role_user (user_id, user_id) WHERE company_id IS NULL');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('role_user');
        Schema::drop('roles');
    }
}
