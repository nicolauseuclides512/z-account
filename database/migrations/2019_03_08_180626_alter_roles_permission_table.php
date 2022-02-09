<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRolesPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn('description', 'label');
            });
        }

        if (Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->renameColumn('description', 'label');
            });
        }

        Schema::table('permission_roles', function (Blueprint $table) {
            $table->dropForeign('permission_roles_role_id_foreign');
            $table->dropForeign('permission_roles_permission_id_foreign');
            $table->dropPrimary('permission_roles_pkey');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_roles');

        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('label', 'description');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->renameColumn('label', 'description');
        });
    }
}
