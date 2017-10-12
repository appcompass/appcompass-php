<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePermissions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('assignable_by_id')->nullable()->unsigned();
            $table->foreign('assignable_by_id')->references('id')->on('permissions'); //->onDelete('cascade')
            $table->boolean('system')->default(false);
            $table->timestamps();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->integer('assignable_by_id')->nullable()->unsigned();
            $table->foreign('assignable_by_id')->references('id')->on('permissions'); //->onDelete('cascade')
        });

        // link permissions to roles
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->integer('permission_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();
        });

        Schema::create('permission_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('permission_id')->unsigned();
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // $table->unique(['user_id', 'permission_id', 'company_id']);
            $table->timestamps();
        });

        DB::connection()
          ->getPdo()
          ->exec('CREATE UNIQUE INDEX permission_user_user_id_permission_id_company_id_unique ON permission_user (user_id, permission_id, company_id) WHERE company_id IS NOT NULL');
        DB::connection()
          ->getPdo()
          ->exec('CREATE UNIQUE INDEX permission_user_user_id_permission_id_unique ON permission_user (user_id, permission_id) WHERE company_id IS NULL');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permission_user');
        Schema::drop('permission_role');
        Schema::drop('permissions');
    }
}
