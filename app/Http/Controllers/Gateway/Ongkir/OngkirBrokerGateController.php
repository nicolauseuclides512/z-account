<?php

namespace App\Http\Controllers\Gateway\Ongkir;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Cores\Jsonable;
use App\Cores\ZHttpClient;
use App\Http\Requests\Ongkir\SearchCityRequest;
use App\Http\Requests\Ongkir\ShippingCostRequest;
use App\Http\Requests\Ongkir\TrackShipmentRequest;
use App\Services\Gateway\Rest\RestService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise;

//INDEPENDENT
class OngkirBrokerGateController
{
    use Jsonable;

    protected $ongkirHeader;

    private $ongkirService;
    private $assetService;

    public function __construct()
    {
        $this->ongkirService = ZHttpClient::init(\config('gateway.connection.ongkir.api_url'));

        $this->assetService = ZHttpClient::init(\config('gateway.connection.asset.api_url'));
    }

    /**
     * @param ShippingCostRequest $request
     * @return mixed
     */
    public function shippingCosts(ShippingCostRequest $request)
    {
        try {

            $promise = [
                'result' => $this
                    ->ongkirService
                    ->requestAsync(
                        'POST',
                        $this->ongkirService->url("ongkir/shipping-costs"),
                        array_merge(
                            ['form_params' => $request->all()]
                        ))];

            $resultUnwrap = Promise\unwrap($promise);

            return $this->jsonGzSuccess(
                $resultUnwrap['result']
            );

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $e) {
            return $this->jsonExceptions($e);
        }
    }


    public function trackShipment(TrackShipmentRequest $request)
    {
        try {

            $promise = [
                'result' => $this
                    ->ongkirService
                    ->requestAsync(
                        'POST',
                        $this->ongkirService->url("ongkir/track-shipment"),
                        array_merge(
                            ['form_params' => $request->all()]
                        ))];

            $resultUnwrap = Promise\unwrap($promise);

            return $this->jsonGzSuccess(
                $resultUnwrap['result']
            );

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function searchCity(SearchCityRequest $request)
    {
        try {

            $service = new RestService(
                new Client([
                    'timeout' => Config::get('gateway.timeout'),
                    'connect_timeout' =>
                        Config::get('gateway.connect_timeout',
                            Config::get('gateway.timeout')
                        )
                ])
            );

            $service->setBaseUri(env('GATEWAY_ASSET_SERVICE'));

            $minChar = $request->get('min_char') ?? 3;

            if (strlen($request->get('q')) >= $minChar) {
                return $service->getAsync('/ongkir/search-cities', $request->all())
                    ->then(
                        function (ResponseInterface $res) {
                            return $this->json(Response::HTTP_OK, 'get city list', $this->safeDecode($res->getBody()));
                        },
                        function (RequestException $e) {
                            return $this->jsonExceptions($e);
                        }
                    )->wait();
            }
            return $this->json(Response::HTTP_OK, 'get city list', []);
        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function searchDistrict(Request $request)
    {
        try {

            $service = new RestService(
                new Client([
                    'timeout' => Config::get('gateway.timeout'),
                    'connect_timeout' =>
                        Config::get('gateway.connect_timeout',
                            Config::get('gateway.timeout')
                        )
                ])
            );

            $service->setBaseUri(env('GATEWAY_ASSET_SERVICE'));

            return $service->getAsync('/pub/districts', $request->input())
                ->then(
                    function (ResponseInterface $res) {
                        return $this->json(Response::HTTP_OK, 'get district list', $this->safeDecode($res->getBody()));
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }
                )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function getCarrierList(Request $request)
    {
        try {

            $service = new RestService(
                new Client([
                    'timeout' => Config::get('gateway.timeout'),
                    'connect_timeout' =>
                        Config::get('gateway.connect_timeout',
                            Config::get('gateway.timeout')
                        )
                ])
            );

            $service->setBaseUri(env('GATEWAY_ASSET_SERVICE'));

            return $service->getAsync('/pub/carriers/list', $request->input())
                ->then(
                    function (ResponseInterface $res) {
                        $data = $this->safeDecode($res->getBody());
                        $this->_filterCarriersList($data['data']);
                        $data['data'] = array_values($data['data']);
                        return $this->json(Response::HTTP_OK, 'get carrier list', $data);
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