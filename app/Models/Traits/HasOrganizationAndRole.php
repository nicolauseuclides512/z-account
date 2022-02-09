<?php
/**
 * @author Jehan Afwazi Ahmad <jehan.afwazi@gmail.com>.
 */


namespace App\Models\Traits;

use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\Permission;


/**
 * Trait HasOrganizationAndRole
 * @package App\Models\Traits
 */
trait HasOrganizationAndRole
{
    /**
     * @var
     */
    private $currentOrg;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizationUser()
    {
        return $this->hasMany(
            OrganizationUser::class,
            'user_id'
        );
    }

    /**
     * @return string
     */
    public function getPrimaryOrganizationAttribute()
    {
        $org = $this
            ->organizationUser()
            ->where('is_primary', true)
            ->with('organization.application')
            ->first();

        return ($org) ? $org->organization : '';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getOrganizationById($id)
    {
        return $this->organizationUser()
            ->where('organization_id', $id)
            ->first();
    }

    /**
     * set current organization
     *
     * @param $orgId
     * @return void
     */
    public function setCurrentOrganization($orgId)
    {
        $this->currentOrg = Organization::find($orgId);
    }

    /**
     * get current organization
     *
     * @return mixed
     */
    public function getCurrentOrganization()
    {
        return $this->currentOrg;
    }

    /**
     * find availability organization in this organization
     * @param $orgId
     * @return bool
     */
    public function hasOrganization($orgId)
    {
        return $this->organizationUser()
                ->whereHas('organization',
                    function ($q) use ($orgId) {
                        return $q->where('id', $orgId);
                    })->count() > 0;
    }

    /**
     * @param $applicationId
     * @return bool
     */
    public function hasApplication($applicationId)
    {
        return $this->organizationUser()
                ->whereHas('organization', function ($q) use ($applicationId) {
                    return $q->where('application_id', $applicationId);
                })->count() > 0;
    }

    /**
     * @param $applicationId
     * @return mixed
     */
    public function getOrganizationByApplicationRef($applicationId)
    {
        return $this->organizationUser()
            ->whereHas('organization', function ($q) use ($applicationId) {
                return $q->where('application_id', $applicationId);
            });
    }

    /**
     * @return mixed
     */
    public function getRelatedOrganizations()
    {
        return $this->organizationUser()->with('organization')->get();
    }

    /**
     * @param Permission $permission
     * @return mixed
     */
    public function hasPermissionInOrganization(Permission $permission)
    {
        $org = $this->organizationUser()
            ->where('organization_id', $this->currentOrg->id)
            ->whereHas(
                'role',
                function ($q) use ($permission) {
                    return $q->whereHas(
                        'permissions', function ($r) use ($permission) {
                        return $r->where('name', $permission->name);
                    });
                })
            ->count();

        return $org;
    }
}
