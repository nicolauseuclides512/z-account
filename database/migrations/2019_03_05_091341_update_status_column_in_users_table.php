<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\SocialProviderUser;
use App\Models\User;

class UpdateStatusColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $provUsers = SocialProviderUser::all();
      foreach ($provUsers as $value) {
        $user = User::find($value->user_id);
        if ($user->status != true) {
          $user->status = true;
          $user->save();
        }
      }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
