<?php

return [
    'users' => [
        'all' => \App\Models\User::STATUS_ALL,
        'inactive' => \App\Models\User::STATUS_INACTIVE,
        'active' => \App\Models\User::STATUS_ACTIVE
    ],
    'organization' => [
        'all' => \App\Models\Organization::STATUS_ALL,
        'inactive' => \App\Models\Organization::STATUS_INACTIVE,
        'active' => \App\Models\Organization::STATUS_ACTIVE
    ],
    'organization_contacts' => [
        'all' => \App\Models\OrganizationContact::STATUS_ALL
    ],

];