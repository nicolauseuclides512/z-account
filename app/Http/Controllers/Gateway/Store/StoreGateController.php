<?php

namespace App\Http\Controllers\Gateway\Store;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Http\Controllers\Gateway\Base\BaseGatewayController;
use App\Services\Gateway\Base\BaseServiceContract;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;

class StoreGateController extends BaseGatewayController
{
    public function __construct(BaseServiceContract $service, Request $request)
    {
        $this->setBaseUri(env('GATEWAY_STORE_SERVICE'));
        parent::__construct($service, $request);
    }

    public function setup()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/setup")
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

    public function guzzleTest()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/guzzle_test")
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

    public function update($id, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{id}/update", $request->input(), ['id' => $id], 'post')
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