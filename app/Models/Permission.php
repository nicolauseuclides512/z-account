<?php

namespace App\Models;

class Permission extends MasterModel
{
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'label'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'permission_roles',
            'permission_id',
            'role_id');
    }

    public static function inst()
    {
        return new self();
    }

}
