<?php
/**
 * @author Arseto Nugroho <satriyo.796@gmail.com>.
 */
namespace App\Http\Controllers\Gateway\Store;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class StockAdjustmentGateController extends StoreGateController
{
    protected $targetUri = '/stock_adjustments';

    public function setupObject(Request $request)
    {
        return $this->postOnly($request, "/setup");
    }

    public function getCreateResource()
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/create", [
                ])->then(
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

    public function storeStockAdjustment(Request $request)
    {
        return $this->postOnly($request);
    }

    public function updateStockAdjustment($id, Request $request)
    {
        try {
            $data = $request->input();
            return $this->service->postAsync(
                $this->targetUri . "/{id}",
                $data, [
                    'id' => $id,
                ]
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

    public function getStockAdjustmentDetail($id)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{id}", [
                    'id' => $id,
                ])->then(
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

    public function deleteStockAdjustment($id)
    {
        try {
            return $this->service->destroyAsync(
                $this->targetUri . "/{id}", [
                    'id' => $id,
                ]
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

    public function itemHistory(Request $request)
    {
        return $this->pagedFilterRequest($request, '/history/item');
    }

    public function reasonHistory(Request $request)
    {
        return $this->pagedFilterRequest($request, '/history/reason');
    }
}
