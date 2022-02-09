<?php

namespace App\Http\Controllers\Gateway\Store;

use GuzzleHttp\Exception\RequestException;
use League\Flysystem\Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */
class AssetUomGateController extends StoreGateController
{
    protected $targetUri = '/uoms';

    public function setDefault($id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{id}/set-default", ['id' => $id])
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