<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddPermissionRoleAccountSeeder extends Seeder
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

            $this->user();
            $this->organizations();
        });
    }


    public function user()
    {
        $pItems = collect([
            ['perm' => 'account-read-profile-user', 'roles' => ['owner']],
            ['perm' => 'account-update-profile-user', 'roles' => ['owner']],
            ['perm' => 'account-upload-photo-profile-user', 'roles' => ['owner']],
            ['perm' => 'account-remove-photo-profile-user', 'roles' => ['owner']],
            ['perm' => 'account-change-password-profile-user', 'roles' => ['owner']],

//            ['perm' => 'account-my-organizations-profile-user', 'roles' => ['owner']],

            ['perm' => 'account-read-my-organization-user', 'roles' => ['owner']],
            ['perm' => 'account-read-users-my-organization-user', 'roles' => ['owner']],
            ['perm' => 'account-set-user-status-in-my-organization-user', 'roles' => ['owner']],
            ['perm' => 'account-read-invited-user-my-organization-user', 'roles' => ['owner']],
            ['perm' => 'account-invite-user-to-my-organization-user', 'roles' => ['owner']],
            ['perm' => 'account-invite-resend-email-my-organization-user', 'roles' => ['owner']],

            ['perm' => 'account-switch-organization-user', 'roles' => ['owner']],

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


    public function organizations()
    {
        $pItems = collect([
            ['perm' => 'account-read-organization', 'roles' => ['owner', 'staff']],
            ['perm' => 'account-read-detail-organization', 'roles' => ['owner', 'staff']],
            ['perm' => 'account-store-organization', 'roles' => ['owner']],
            ['perm' => 'account-update-organization', 'roles' => ['owner', 'staff']],
            ['perm' => 'account-destroy-bulk-organization', 'roles' => ['owner']],
            ['perm' => 'account-mark-as-bulk-organization', 'roles' => ['owner']],

            ['perm' => 'account-update-primary-organization', 'roles' => ['owner', 'staff']],
            ['perm' => 'account-upload-logo-organization', 'roles' => ['owner', 'staff']],
            ['perm' => 'account-remove-logo-organization', 'roles' => ['owner', 'staff']],

            ['perm' => 'account-read-contact-organization', 'roles' => ['owner']],
            ['perm' => 'account-read-contact-detail-organization', 'roles' => ['owner']],
            ['perm' => 'account-store-contact-organization', 'roles' => ['owner']],
            ['perm' => 'account-update-contact-organization', 'roles' => ['owner']],
            ['perm' => 'account-destroy-contact-organization', 'roles' => ['owner']],
            ['perm' => 'account-set-primary-contact-organization', 'roles' => ['owner']],
            ['perm' => 'account-resend-verification-contact-organization', 'roles' => ['owner']],
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
