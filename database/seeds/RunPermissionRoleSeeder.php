<?php

use Illuminate\Database\Seeder;

class RunPermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AddPermissionRoleAssetSeeder::class);
        $this->call(AddPermissionRoleAccountSeeder::class);
        $this->call(AddPermissionRoleReportSeeder::class);
        $this->call(AddPermissionRoleStoreSeeder::class);
        $this->call(AddPermissionRoleOngkirSeeder::class);
    }
}
