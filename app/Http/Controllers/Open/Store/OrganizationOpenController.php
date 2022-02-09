<?php

namespace App\Http\Controllers\Open\Store;

use App\Cores\Jsonable;
use App\Cores\TokenGenerator;
use App\Models\Application;
use App\Models\Role;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OrganizationOpenController extends Controller
{

    use Jsonable;

    private $url, $header;

    public function getMyOrganizations()
    {
        $relatedOrg = Auth::User()->getRelatedOrganizationsOpen();

        return $this->json(
            Response::HTTP_OK,
            "fetch my organization successfully.",
            $relatedOrg);
    }

}
