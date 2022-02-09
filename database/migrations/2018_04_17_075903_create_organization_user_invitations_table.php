<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationUserInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomBlueprint::inst()->create('organization_user_invitations',
            function (CustomBlueprint $table) {
                $table->bigIncrements('id');
                $table->string('token')->unique();
                $table->dateTime('expired_token');
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('organization_id')->unsigned();
                $table->bigInteger('role_id')->unsigned();
                $table->boolean('is_reset_password')->default(false);
                $table->boolean('status')->default(false);
                $table->defaultColumn();

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles');

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users');

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_user_invitations');
    }
}
