<?php

namespace App\Http\Controllers\Gateway\Store;

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */
class AssetAccountGateController extends StoreGateController
{
    protected $targetUri = '/accounts';

    public function getUser()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/user")
                ->then(
                    function (ResponseInterface $res) {
                        return $this->jsonGzSuccess($res);
                    },
                    function (RequestException $e) {
                        throw new AppException($e->getMessage(), $e->getCode());
                    }
                )->wait();

        } catch (\Exception $e) {
            return $this->jsonEXceptions($e);
        }
    }
}