<?php

namespace App\Http\Controllers\Gateway\Asset\Pub;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Exceptions\AppException;
use App\Http\Controllers\Gateway\Base\BaseGatewayController;
use App\Services\Gateway\Base\BaseServiceContract;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;

class AssetGatePubController extends BaseGatewayController
{
    protected $isStrict = false;

    public function __construct(BaseServiceContract $service, Request $request)
    {
        parent::__construct($service, $request);
        $this->service->setBaseUri(env('GATEWAY_ASSET_SERVICE'));
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