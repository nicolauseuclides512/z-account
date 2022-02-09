<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //role
        $admin = \App\Models\Role::inst();
        $admin->name = 'ADMIN';
        $admin->label = 'full access';
        $admin->save();

        $owner = \App\Models\Role::where('name', 'OWNER')->first();
        $staff = \App\Models\Role::where('name', 'STAFF')->first();

        //permission
        $per_create_contact = \App\Models\Permission::inst();
        $per_create_contact->name = 'create-contact';
        $per_create_contact->label = 'create-contact';
        $per_create_contact->save();

        $per_update_contact = \App\Models\Permission::inst();
        $per_update_contact->name = 'update-contact';
        $per_update_contact->label = 'update-contact';
        $per_update_contact->save();

        $per_delete_contact = \App\Models\Permission::inst();
        $per_delete_contact->name = 'delete-contact';
        $per_delete_contact->label = 'delete-contact';
        $per_delete_contact->save();

        $per_read_contact = \App\Models\Permission::inst();
        $per_read_contact->name = 'read-contact';
        $per_read_contact->label = 'read-contact';
        $per_read_contact->save();

        $per_mark_as_bulk_contact = \App\Models\Permission::inst();
        $per_mark_as_bulk_contact->name = 'mark-as-bulk-contact';
        $per_mark_as_bulk_contact->label = 'mark-as-bulk-contact';
        $per_mark_as_bulk_contact->save();

        $per_destroy_bulk_contact = \App\Models\Permission::inst();
        $per_destroy_bulk_contact->name = 'destroy-bulk-contact';
        $per_destroy_bulk_contact->label = 'destroy-bulk-contact';
        $per_destroy_bulk_contact->save();

        $per_import_data_contact = \App\Models\Permission::inst();
        $per_import_data_contact->name = 'import-data-contact';
        $per_import_data_contact->label = 'import-data-contact';
        $per_import_data_contact->save();

        //role permission admin
        $admin->permissions()->save($per_create_contact);
        $admin->permissions()->save($per_update_contact);
        $admin->permissions()->save($per_delete_contact);
        $admin->permissions()->save($per_read_contact);
        $admin->permissions()->save($per_import_data_contact);
        $admin->permissions()->save($per_mark_as_bulk_contact);
        $admin->permissions()->save($per_destroy_bulk_contact);

        //role permission owner
        $owner->permissions()->save($per_create_contact);
        $owner->permissions()->save($per_update_contact);
        $owner->permissions()->save($per_delete_contact);
        $owner->permissions()->save($per_read_contact);
        $owner->permissions()->save($per_import_data_contact);
        $owner->permissions()->save($per_mark_as_bulk_contact);
        $owner->permissions()->save($per_destroy_bulk_contact);

        //role permission staff
        $staff->permissions()->save($per_create_contact);
        $staff->permissions()->save($per_update_contact);
        $staff->permissions()->save($per_read_contact);

    }
}
