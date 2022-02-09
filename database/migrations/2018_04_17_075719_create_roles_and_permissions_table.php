<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesAndPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomBlueprint::inst()->create('roles', function (CustomBlueprint $table) {
            $table->bigIncrements('id');
            $table->string("name", 50)->unique();
            $table->string('description', 500)->nullable();
            $table->defaultColumn();
        });

        CustomBlueprint::inst()->create('permissions', function (CustomBlueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('endpoint_url')->nullable();
            $table->string('description', 500)->nullable();
            $table->defaultColumn();
        });

        CustomBlueprint::inst()->create('permission_roles', function (CustomBlueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();
            $table->defaultColumn();

            $table->foreign('role_id')->references('id')
                ->on('roles');

            $table->foreign('permission_id')->references('id')
                ->on('permissions');
        });

        //ADD ROLE DATA
        \App\Models\Role::create([
            'id' => 1,
            'name' => 'OWNER'
        ]);

        \App\Models\Role::create([
            'id' => 2,
            'name' => 'STAFF'
        ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('permission_roles');
    }
}
