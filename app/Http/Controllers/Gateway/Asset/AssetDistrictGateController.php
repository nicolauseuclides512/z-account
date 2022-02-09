<?php

namespace App\Http\Controllers\Gateway\Asset;

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class AssetDistrictGateController extends AssetGateController
{
    protected $targetUri = '/districts';

    public function getByProvince($id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/provinces/{id}", ['id' => $id])
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
