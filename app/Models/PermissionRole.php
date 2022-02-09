<?php

namespace App\Models;

class PermissionRole extends MasterModel
{
    protected $table = 'permission_roles';

    public function role()
    {
        return $this->belongsTo(
            Role::class, 'id');
    }

    public function permission()
    {
        return $this->belongsTo(
            Permission::class, 'id');
    }
}
