<?php

use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Role::create([
            'id' => 1,
            'name' => 'OWNER'
        ]);

        \App\Models\Role::create([
            'id' => 2,
            'name' => 'STAFF'
        ]);
    }
}
