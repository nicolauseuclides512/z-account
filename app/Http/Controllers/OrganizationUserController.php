<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\BaseController;
use App\Models\OrganizationUser;
use Illuminate\Http\Request;

class OrganizationUserController extends BaseController
{
    public $name = 'Organization User';

    public $sortBy = ['id', 'name', 'created_at', 'updated_at'];

    public function __construct(Request $request)
    {
        parent::__construct(OrganizationUser::inst(), $request);
    }
}
