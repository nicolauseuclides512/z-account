<?php

namespace App\Http\Controllers\Gateway\Asset\Pub;

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class AssetCountryPubGateController extends AssetGatePubController
{
    protected $targetUri = '/pub/countries';

    public function nestedList()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/nested_list")
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
