<?php
/**
 * @author Arseto Nugroho <satriyo.796@gmail.com>.
 */
namespace App\Http\Controllers\Gateway\Store;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class LazadaAPIConfigGateController extends StoreGateController
{
    protected $targetUri = '/integration/lazada/api-config';

    public function store(Request $request)
    {
        return $this->postOnly($request);
    }

    public function detail()
    {
        try {
            return $this->service->getAsync(
                $this->targetUri)
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

    public function delete()
    {
        try {
            return $this->service->destroyAsync($this->targetUri)
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
