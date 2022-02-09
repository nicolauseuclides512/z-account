<?php

namespace App\Http\Controllers\Gateway\Report;

use App\Exceptions\AppException;
use App\Http\Controllers\Gateway\Base\BaseGatewayController;
use App\Services\Gateway\Base\BaseServiceContract;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Psr\Http\Message\ResponseInterface;

class SalesOrderReportGateController extends BaseGatewayController
{
    public function __construct(BaseServiceContract $service, Request $request)
    {
        $this->setBaseUri(env('GATEWAY_REPORT_SERVICE'));
        parent::__construct($service, $request);
    }

    public function byMonth()
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/salesorder/by-month",
                $this->request->input()
            )->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    throw new AppException($e->getMessage(), $e->getCode());
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function byItem()
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/salesorder/by-item",
                $this->request->input()
            )->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    throw new AppException($e->getMessage(), $e->getCode());
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function byCustomer()
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/salesorder/by-customer",
                $this->request->input()
            )->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    throw new AppException($e->getMessage(), $e->getCode());
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function total()
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/salesorder/total",
                $this->request->input()
            )->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    throw new AppException($e->getMessage(), $e->getCode());
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

}
