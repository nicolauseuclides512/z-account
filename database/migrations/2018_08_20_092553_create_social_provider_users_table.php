<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialProviderUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomBlueprint::inst()
            ->create('social_provider_users',
                function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->bigInteger('user_id')->unsigned();
                    $table->foreign('user_id')->references('id')->on('users');
                    $table->string('provider_id');
                    $table->string('provider');
                    $table->unique(['provider_id', 'provider']);

                    $table->defaultColumn();

                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_provider_users');
    }
}
