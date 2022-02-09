<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddPermissionRoleReportSeeder extends Seeder
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

            $this->report();
        });
    }

    private function report()
    {
        $pItems = collect([
            ['perm' => 'report-by-month', 'roles' => ['owner']],
            ['perm' => 'report-by-item', 'roles' => ['owner']],
            ['perm' => 'report-by-customer', 'roles' => ['owner']],
            ['perm' => 'report-total', 'roles' => ['owner']],
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
