<?php

namespace App\Http\Controllers\Gateway\Asset;

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class AssetProvinceGateController extends AssetGateController
{
    protected $targetUri = '/provinces';

    public function getByCountry($id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/countries/{id}", ['id' => $id])
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
