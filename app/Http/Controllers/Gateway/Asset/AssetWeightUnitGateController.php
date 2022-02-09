<?php

namespace App\Http\Controllers\Gateway\Asset;

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class AssetWeightUnitGateController extends AssetGateController
{
    protected $targetUri = '/weight_units';

    public function getByCode($code)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/code/{code}", ['code' => $code])
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
