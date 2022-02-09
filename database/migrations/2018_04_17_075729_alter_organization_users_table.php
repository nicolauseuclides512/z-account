<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrganizationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organization_users', function (Blueprint $table) {
            $table->bigInteger('role_id')->default(1)->unsigned();
            //1. ACTIVE, 2. INACTIVE, -1. DELETED
            $table->tinyInteger('status')->default(1);
            $table->dropColumn('scope_id');

            $table->foreign('role_id')->references('id')
                ->on('roles');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_users', function (Blueprint $table) {
            $table->dropColumn('role_id');
            $table->dropColumn('status');
            $table->bigInteger('scope_id')->nullable();
        });
    }
}
