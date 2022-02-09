<?php
/**
 * @author Arseto Nugroho <satriyo.796@gmail.com>.
 */
namespace App\Http\Controllers\Gateway\Store;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class LazadaItemAliasGateController extends StoreGateController
{
    protected $targetUri = '/integration/lazada/item/{item_id}/aliases';

    public function fetchItemAlias($item_id)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri, [
                    'item_id' => $item_id,
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

    public function storeItemAlias($item_id, Request $request)
    {
        try {
            $data = $request->input();
            return $this->service->postAsync(
                $this->targetUri,
                $data, [
                    'item_id' => $item_id,
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

    public function detailItemAlias($item_id, $id)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{id}", [
                    'item_id' => $item_id,
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

    public function updateItemAlias($item_id, $id, Request $request)
    {
        try {
            $data = $request->input();
            return $this->service->postAsync(
                $this->targetUri . "/{id}",
                $data, [
                    'item_id' => $item_id,
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

    public function deleteItemAlias($item_id, $id)
    {
        try {
            return $this->service->destroyAsync(
                $this->targetUri . "/{id}", [
                    'item_id' => $item_id,
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
}
