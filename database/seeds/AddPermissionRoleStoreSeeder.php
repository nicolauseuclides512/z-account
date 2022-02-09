<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddPermissionRoleStoreSeeder extends Seeder
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
            $this->accounts();
            $this->categories();
            $this->paymentTerm();
            $this->uoms();
            $this->taxes();
            $this->salutations();
            $this->salesPersons();
            $this->attributes();
            $this->settings();
            $this->contacts();
            $this->item();
            $this->salesOrders();
            $this->invoices();
            $this->payments();
            $this->shipments();
            $this->stockAdjustments();
            $this->stocks();
            $this->myChannels();
            $this->reasons();
            $this->quickReplyCategory();
            $this->quickReply();
            $this->salesChannels();
            $this->lazada();
        });
    }

    private function setup()
    {
        $pItems = collect([
            ['perm' => 'store-init-setup', 'roles' => ['owner']],
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

    private function accounts()
    {
        $pItems = collect([
            ['perm' => 'store-read-account', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-account', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-account', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-account', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-account', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-account', 'roles' => ['owner', 'staff']],
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

    private function categories()
    {
        $pItems = collect([
            ['perm' => 'store-read-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-category', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-category', 'roles' => ['owner', 'staff']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function paymentTerm()
    {
        $pItems = collect([
            ['perm' => 'store-read-payment-term', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-payment-term', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-payment-term', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-payment-term', 'roles' => ['owner']],
            ['perm' => 'store-destroy-bulk-payment-term', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-payment-term', 'roles' => ['owner']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function uoms()
    {
        $pItems = collect([
            ['perm' => 'store-set-default-uom', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-uom', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-uom', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-uom', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-uom', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-uom', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-uom', 'roles' => ['owner', 'staff']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function taxes()
    {
        $pItems = collect([
            ['perm' => 'store-read-tax', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-tax', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-tax', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-tax', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-tax', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-tax', 'roles' => ['owner', 'staff']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function salutations()
    {
        $pItems = collect([
            ['perm' => 'store-read-salutation', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-salutation', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-salutation', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-salutation', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-salutation', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-salutation', 'roles' => ['owner', 'staff']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function salesPersons()
    {
        $pItems = collect([
            ['perm' => 'store-read-sales-person', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-sales-person', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-sales-person', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-sales-person', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-sales-person', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-sales-person', 'roles' => ['owner', 'staff']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function attributes()
    {
        $pItems = collect([
            ['perm' => 'store-read-attribute', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-attribute', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-attribute', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-attribute', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-attribute', 'roles' => ['owner']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function settings()
    {
        $pItems = collect([
            ['perm' => 'store-edit-setting', 'roles' => ['owner']],
            ['perm' => 'store-store-detail-setting', 'roles' => ['owner']],
            ['perm' => 'store-set-checkout-setting', 'roles' => ['owner']],
            ['perm' => 'store-set-shipping-setting', 'roles' => ['owner']],
            ['perm' => 'store-set-taxes-setting', 'roles' => ['owner']],
            ['perm' => 'store-add-payment-method-setting', 'roles' => ['owner']],
            ['perm' => 'store-destroy-payment-method-setting', 'roles' => ['owner']],
            ['perm' => 'store-add-payment-method-detail-setting', 'roles' => ['owner']],
            ['perm' => 'store-destroy-payment-method-detail-setting', 'roles' => ['owner']],
            ['perm' => 'store-add-bank-transfer-payment-setting', 'roles' => ['owner']],
            ['perm' => 'store-destroy-bank-transfer-payment-setting', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function contacts()
    {
        $pItems = collect([
            ['perm' => 'store-read-contact', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-contact', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-contact', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-contact', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-contact', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-contact', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-import-data-contact', 'roles' => ['owner', 'staff']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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


    private function item()
    {
        $pItems = collect([
            ['perm' => 'store-get-credential-upload-item', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-item', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-item', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-item', 'roles' => ['owner']],
            ['perm' => 'store-update-item', 'roles' => ['owner']],
            ['perm' => 'store-destroy-bulk-item', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-item', 'roles' => ['owner']],
            ['perm' => 'store-add-attribute-item', 'roles' => ['owner']],
            ['perm' => 'store-update-attribute-key-item', 'roles' => ['owner']],
            ['perm' => 'store-destroy-attribute-value-item', 'roles' => ['owner']],
            ['perm' => 'store-update-price-item', 'roles' => ['owner']],
            ['perm' => 'store-update-inventory-stock-item', 'roles' => ['owner']],
            ['perm' => 'store-add-image-item', 'roles' => ['owner']],
            ['perm' => 'store-destroy-image-item', 'roles' => ['owner']],
            ['perm' => 'store-set-primary-image-item', 'roles' => ['owner']],
            ['perm' => 'store-import-mass-item', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function salesOrders()
    {
        $pItems = collect([
            ['perm' => 'store-read-sales-order', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-sales-order', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-sales-order', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-sales-order', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-sales-order', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-sales-order', 'roles' => ['owner']],
            ['perm' => 'store-read-sales-order-detail-sales-order', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-detail-sales-order', 'roles' => ['owner', 'staff']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function invoices()
    {
        $pItems = collect([
            ['perm' => 'store-read-by-sales-order-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-by-sales-order-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-generate-pdf-by-sales-order-and-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-generate-pdf-bulk-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-mark-as-sent-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-mark-as-void-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-send-email-by-sales-order-in-detail-invoice', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-send-email-by-sales-order-and-invoice', 'roles' => ['owner', 'staff']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function payments()
    {
        $pItems = collect([
            ['perm' => 'store-read-by-invoice-payment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-by-invoice-payment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-by-invoice-payment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-by-invoice-payment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-by-invoice-payment', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function shipments()
    {
        $pItems = collect([
            ['perm' => 'store-read-by-sales-order-shipment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-by-sales-order-shipment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-by-sales-order-shipment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-by-sales-order-shipment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-by-sales-order-shipment', 'roles' => ['owner']],
            ['perm' => 'store-generate-shipment-label-pdf-bulk-shipment', 'roles' => ['owner', 'staff']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function stockAdjustments()
    {
        $pItems = collect([
            ['perm' => 'store-setup-object-stock-adjustment', 'roles' => ['owner']],
            ['perm' => 'store-read-stock-adjustment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-stock-adjustment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-stock-adjustment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-stock-adjustment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-stock-adjustment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-history-item-stock-adjustment', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-history-reason-stock-adjustment', 'roles' => ['owner', 'staff']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function stocks()
    {
        $pItems = collect([
            ['perm' => 'store-read-stock', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-stock', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-free-adjust-stock', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function myChannels()
    {
        $pItems = collect([
            ['perm' => 'store-read-my-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-my-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-my-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-my-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-my-channel', 'roles' => ['owner']],

        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function reasons()
    {
        $pItems = collect([
            ['perm' => 'store-read-reason', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-reason', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-reason', 'roles' => ['owner']],
            ['perm' => 'store-update-reason', 'roles' => ['owner']],
            ['perm' => 'store-destroy-reason', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function quickReplyCategory()
    {
        $pItems = collect([
            ['perm' => 'store-read-quick-reply-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-quick-reply-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-quick-reply-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-quick-reply-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-quick-reply-category', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-mark-as-bulk-quick-reply-category', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function quickReply()
    {
        $pItems = collect([
            ['perm' => 'store-read-quick-reply', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-quick-reply', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-quick-reply', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-quick-reply', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-destroy-bulk-quick-reply', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-quick-reply', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function salesChannels()
    {
        $pItems = collect([
            ['perm' => 'store-read-sales-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-read-detail-sales-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-store-sales-channel', 'roles' => ['owner', 'staff']],
            ['perm' => 'store-update-sales-channel', 'roles' => ['owner']],
            ['perm' => 'store-destroy-bulk-sales-channel', 'roles' => ['owner']],
            ['perm' => 'store-mark-as-bulk-sales-channel', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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

    private function lazada()
    {
        $pItems = collect([
            ['perm' => 'integration-store-api-config-lazada', 'roles' => ['owner']],
            ['perm' => 'integration-read-detail-api-config-lazada', 'roles' => ['owner']],
            ['perm' => 'integration-destroy-api-config-lazada', 'roles' => ['owner']],

            ['perm' => 'integration-read-item-alias-item-lazada', 'roles' => ['owner']],
            ['perm' => 'integration-store-item-alias-item-lazada', 'roles' => ['owner']],
            ['perm' => 'integration-read-detail-item-alias-item-lazada', 'roles' => ['owner']],
            ['perm' => 'integration-update-item-alias-item-lazada', 'roles' => ['owner']],
            ['perm' => 'integration-destroy-item-alias-item-lazada', 'roles' => ['owner']],
        ]);

        $pItems->each(function ($o) {
            $perm = Permission::create([
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
