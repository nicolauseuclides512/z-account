<?php

namespace App\Models;

class SocialProviderUser extends MasterModel
{
    protected $table = 'social_provider_users';

    protected $fillable = [
        'user_id',
        'provider_id',
        'provider',
        'access_token'
    ];

    public static function inst()
    {
        return new self();
    }
}
