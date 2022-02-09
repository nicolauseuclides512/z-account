<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;


/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */
class OauthClientTableSeeder extends Seeder
{
    public function run()
    {
        $existingCliPersonal = DB::table('oauth_clients')
            ->where('name', 'Personal Access Client')
            ->exists();

        if (!$existingCliPersonal) {
            echo "create new personal access client";
            (new Client)->forceFill([
                'id' => 1,
                'user_id' => null,
                'name' => 'Personal Access Client',
                'secret' => 'najGxbgGxsHmRipXmS43tFpehXEygY8RYWlusLDK',
                'redirect' => 'http://localhost',
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
            ])->save();
        }

        $existingCliPassword = DB::table('oauth_clients')
            ->where('name', 'Password Grant Client')
            ->exists();

        if (!$existingCliPassword) {
            echo "create new password grant client";

            (new Client)->forceFill([
                'id' => 2,
                'user_id' => null,
                'name' => 'Password Grant Client',
                'secret' => 'beXvmNU9dQS1cN35vmGSSDAfOR8nSVASouE3sVBT',
                'redirect' => 'http://localhost',
                'personal_access_client' => false,
                'password_client' => true,
                'revoked' => false,
            ])->save();
        }

        $existingOauthPersonalAccessClient = DB::table('oauth_personal_access_clients')
            ->where('client_id', 1)
            ->exists();

        if (!$existingOauthPersonalAccessClient) {
            echo "create new oauth personal access client";
            DB::insert("INSERT INTO oauth_personal_access_clients (id,client_id,created_at,updated_at) VALUES (1,1,NOW(),NOW())");
        }

    }
}