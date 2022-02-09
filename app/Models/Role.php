<?php

namespace App\Models;

class Role extends MasterModel
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'label'
    ];

    public function organizationUser()
    {
        return $this->hasMany(
            OrganizationUser::class,
            'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_roles',
            'role_id',
            'permission_id');
    }

    public function givePermissionTo(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }

    public function scopeGetByName($q, $name)
    {
        return $q->where('name', strtoupper($name))->firstOrFail();
    }

    public static function inst()
    {
        return new self();
    }
}
