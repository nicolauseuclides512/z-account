<?php
/**
 * @author Arseto Nugroho <satriyo.796@gmail.com>.
 */
namespace App\Http\Controllers\Gateway\Store;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class StockGateController extends StoreGateController
{
    protected $targetUri = '/stocks';

    public function detail(Request $request)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . '/detail',
                $request->input()
            )->then(
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

    public function freeAdjust(Request $request)
    {
        return $this->postOnly($request, '/free_adjust');
    }

}
