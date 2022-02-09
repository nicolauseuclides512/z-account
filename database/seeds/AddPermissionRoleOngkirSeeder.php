<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddPermissionRoleOngkirSeeder extends Seeder
{
    private $owner, $staff;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $this->owner = \App\Models\Role::where('name', 'OWNER')->first();
            $this->staff = \App\Models\Role::where('name', 'STAFF')->first();

            $this->ongkir();
        });
    }


    public function ongkir()
    {
        $pItems = collect([
            ['perm' => 'ongkir-shipping-cost', 'roles' => ['owner', 'staff']],
            ['perm' => 'ongkir-track-shipment', 'roles' => ['owner', 'staff']]
        ]);

        $pItems->each(function ($o) {
            $perm = $perm = Permission::create([
                'name' => $o['perm'],
                'label' => $o['perm']
            ]);

            $role = collect($o['roles']);

            if ((bool)$role->contains('owner'))
                $this->owner->permissions()->save($perm);

            if ((bool)$role->contains('staff'))
                $this->staff->permissions()->save($perm);
        });

    }

}
