<?php

namespace App\Http\Controllers\Gateway\Store;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;


class SettingGateController extends StoreGateController
{
    protected $targetUri = '/settings';

    public function getEdit()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/edit", [])->then(
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

    public function storeDetail(Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/store_detail", $request->input())->then(
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

    public function setCheckout(Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/checkout", $request->input())->then(
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

    public function setShipping(Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/shipping", $request->input())->then(
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

    public function setTaxes(Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/tax", $request->input())->then(
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

    public function addPaymentMethod(Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/payments", $request->input())->then(
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

    public function destroyPaymentMethod(Request $request)
    {
        try {
            $id = $request->get('id');

            if (!$id) {
                throw new AppException('id param not found.', Response::HTTP_BAD_REQUEST);
            }

            return $this->service->destroyAsync($this->targetUri . "/payments", [
                'id' => $id
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

    public function addPaymentMethodDetail($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/payments/{id}/add_detail",
                $request->input(), [
                    'id' => $id
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

    public function destroyPaymentMethodDetail(Request $request)
    {
        try {
            $id = $request->get('id');

            if (!$id) {
                throw new AppException('id param not found.', Response::HTTP_BAD_REQUEST);
            }

            return $this->service->destroyAsync($this->targetUri . "/payments/remove_detail", [
                'id' => $id
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
}