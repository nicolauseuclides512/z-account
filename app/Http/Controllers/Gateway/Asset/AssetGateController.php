<?php

namespace App\Http\Controllers\Gateway\Asset;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Exceptions\AppException;
use App\Http\Controllers\Gateway\Base\BaseGatewayController;
use App\Services\Gateway\Base\BaseServiceContract;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;

class AssetGateController extends BaseGatewayController
{
    public function __construct(BaseServiceContract $service, Request $request)
    {
        $this->setBaseUri(env('GATEWAY_ASSET_SERVICE'));
        parent::__construct($service, $request);
    }

    public function list()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/list")
                ->then(
                    function (ResponseInterface $res) {
                        return $this->jsonGzSuccess($res);
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }
                )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

}