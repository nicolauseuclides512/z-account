<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddPermissionRoleAssetSeeder extends Seeder
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

            $this->country();
            $this->province();
            $this->district();
            $this->region();
            $this->bank();
            $this->carrier();
            $this->currency();
            $this->paymentMethod();
            $this->language();
            $this->timezone();
            $this->weighUnit();
        });
    }

    private function country()
    {
        $pItems = collect([


            ['perm' => 'asset-nested-list-country', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-list-country', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-country', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-country', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-country', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-country', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-country', 'roles' => ['owner', 'staff']],
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

    private function province()
    {
        $pItems = collect([


            ['perm' => 'asset-list-province', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-by-country-province', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-province', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-province', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-province', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-province', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-province', 'roles' => ['owner', 'staff']],
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

    private function district()
    {
        $pItems = collect([


            ['perm' => 'asset-list-district', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-by-province-district', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-district', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-district', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-district', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-district', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-district', 'roles' => ['owner', 'staff']],
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

    private function region()
    {
        $pItems = collect([


            ['perm' => 'asset-list-region', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-by-district-region', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-search-cities-region', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-region', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-region', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-region', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-region', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-region', 'roles' => ['owner', 'staff']],
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

    private function carrier()
    {
        $pItems = collect([

            ['perm' => 'asset-list-carrier', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-carrier', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-carrier', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-carrier', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-carrier', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-carrier', 'roles' => ['owner', 'staff']],
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

    private function bank()
    {
        $pItems = collect([

            ['perm' => 'asset-list-bank', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-bank', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-bank', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-bank', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-bank', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-bank', 'roles' => ['owner', 'staff']],
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

    private function paymentMethod()
    {
        $pItems = collect([
            ['perm' => 'asset-list-payment-method', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-payment-method', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-payment-method', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-payment-method', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-payment-method', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-payment-method', 'roles' => ['owner', 'staff']],
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

    private function weighUnit()
    {
        $pItems = collect([

            ['perm' => 'asset-read-by-code-weight-unit', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-list-weight-unit', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-weight-unit', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-weight-unit', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-weight-unit', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-weight-unit', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-weight-unit', 'roles' => ['owner', 'staff']],
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

    private function language()
    {
        $pItems = collect([
            ['perm' => 'asset-list-language', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-language', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-language', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-language', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-language', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-language', 'roles' => ['owner', 'staff']],
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

    private function timezone()
    {
        $pItems = collect([
            ['perm' => 'asset-list-timezone', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-timezone', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-timezone', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-timezone', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-timezone', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-timezone', 'roles' => ['owner', 'staff']],
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

    private function currency()
    {
        $pItems = collect([
            ['perm' => 'asset-list-currency', 'roles' => ['owner', 'staff']],

            ['perm' => 'asset-read-currency', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-read-detail-currency', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-store-currency', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-update-currency', 'roles' => ['owner', 'staff']],
            ['perm' => 'asset-destroy-currency', 'roles' => ['owner', 'staff']],
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
