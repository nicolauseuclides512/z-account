<?php
/**
 * @author Arseto Nugroho <satriyo.796@gmail.com>.
 */
namespace App\Http\Controllers\Gateway\Store;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class MySalesChannelGateController extends StoreGateController
{
    protected $targetUri = '/my_channels';

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

    public function storeMySalesChannel(Request $request)
    {
        return $this->postOnly($request);
    }

    public function updateMySalesChannel($id, Request $request)
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

    public function getMySalesChannelDetail($id)
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

    public function deleteMySalesChannel($id)
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
}
