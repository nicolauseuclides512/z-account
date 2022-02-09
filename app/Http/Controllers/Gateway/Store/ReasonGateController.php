<?php
/**
 * @author Arseto Nugroho <satriyo.796@gmail.com>.
 */
namespace App\Http\Controllers\Gateway\Store;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class ReasonGateController extends StoreGateController
{
    protected $targetUri = '/reasons';

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

    public function storeReason(Request $request)
    {
        return $this->postOnly($request);
    }

    public function fetch(Request $request)
    {
        try {
            return $this->service->getAsync($this->targetUri,
                [ 'category_code' => $request->get('category_code') ]
            )
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

    public function getReasonDetail($id)
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

    public function updateReason($id, Request $request)
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

    public function deleteReason($id)
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
