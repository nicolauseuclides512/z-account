<?php

namespace App\Http\Controllers\Gateway\RajaOngkir;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Http\Controllers\Gateway\Base\BaseGatewayController;
use App\Services\Gateway\Base\BaseServiceContract;
use App\Services\Gateway\Rest\RestService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Message\ResponseInterface;


class RajaOngkirGateController extends BaseGatewayController
{
    protected $targetUri = '/';

    public function __construct(BaseServiceContract $baseServiceContract, Request $request)
    {
//        parent::__construct($baseServiceContract, $request);
    }

    private function _filter(array &$data)
    {
        foreach ($data as $key => &$kurir) {
            if (!empty($kurir['costs'])) {
                foreach ($kurir['costs'] as &$costs) {
                    if (!empty($costs['cost'])) {
                        foreach ($costs['cost'] as &$cost) {
                            if (empty($cost['value'])) {
                                unset($data[$key]);
                            }
                            if (strpos($cost['etd'], 'HARI') === false && strpos($cost['etd'], 'JAM') === false && strpos($cost['etd'], 'Hari') === false && strpos($cost['etd'], 'Jam') === false) {
                                $cost['etd'] = trim($cost['etd']) . ' HARI';
                            }
                        }
                    } else {
                        unset($data[$key]);
                    }
                }
            } else {
                unset($data[$key]);
            }
        }
        return $data;
    }

    private function _filterCourier(array &$data)
    {
        foreach ($data as $key => &$kurir) {
            if (strpos($kurir['code'], 'jne') === false && strpos($kurir['code'], 'pos') === false
                && strpos($kurir['code'], 'tiki') === false && strpos($kurir['code'], 'J&T') === false
                && strpos($kurir['code'], 'wahana') === false && strpos($kurir['code'], 'pandu') === false
                && strpos($kurir['code'], 'sicepat') === false) {
                unset($data[$key]);
            } else {
                foreach ($kurir['costs'] as $subkey => &$costs) {
                    if (strpos($costs['service'], 'ONS') === false && strpos($costs['service'], 'REG') === false
                        && strpos($costs['service'], 'Paket Kilat Khusus') === false && strpos($costs['service'], 'Express Next Day Barang') === false
                        && strpos($costs['service'], 'EZ') === false && strpos($costs['service'], 'OKE') === false
                        && strpos($costs['service'], 'YES') === false && strpos($costs['service'], 'DES') === false
                        && strpos($costs['service'], 'BEST') === false && strpos($costs['service'], 'CTC') === false) {
                        unset($kurir['costs'][$subkey]);
                    }
                    if (strpos($costs['service'], 'Paket Kilat Khusus') !== false) {
                        $costs['service'] = trim($costs['service'], 'Paket ');
                    }
                    if (strpos($costs['service'], 'Express Next Day Barang') !== false) {
                        $costs['service'] = trim($costs['service'], ' Next Day Barang');
                    }
                }
            }
        }
        return $data;
    }

    private function _combineArray(array $data)
    {
        $output = [];
        foreach ($data as $kurir) {
            $main_fields = array('code' => $kurir['code'], 'name' => $kurir['name']);
            foreach ($kurir['costs'] as $costs) {
                $tCosts = $costs['cost'][0];
                unset($costs['cost']);
                $tmp = array_merge($main_fields, $costs, $tCosts);
                array_push($output, $tmp);
            }
        }
        return $output;
    }

    private function _arrayValuesRecursive(array $data)
    {
        foreach ($data as $key => $kurir) {
            if (is_array($kurir)) {
                $data[$key] = $this->_arrayValuesRecursive($kurir);
            }
        }

        if (isset($data['costs'])) {
            $data['costs'] = array_values($data['costs']);
        }

        return $data;
    }

    private function _filterCarriersList(array &$data)
    {
        foreach ($data as $key => &$carriers) {
            if (strpos($carriers['code'], 'jne') === false && strpos($carriers['code'], 'pos') === false
                && strpos($carriers['code'], 'tiki') === false && strpos($carriers['code'], 'wahana') === false
                && strpos($carriers['code'], 'jnt') === false && strpos($carriers['code'], 'sicepat') === false) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    private function _getDomesticCostsWahana(Request $request)
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

            $service->setBaseUri(env('GATEWAY_RAJAONGKIR_SERVICE'));

            $service->setHeaders([
                'headers' => [
                    'key' => 'c29168581f10f43d3eede488864a573c'
                ]
            ]);

            $validate = Validator::make($request->input(), [
                'origin' => 'required|integer',
                'originType' => 'required|string',
                'destination' => 'required|integer',
                'destinationType' => 'required|string',
                'weight' => 'required|integer',
                'courier' => 'required|string',
                'length' => 'required|integer',
                'width' => 'required|integer',
                'height' => 'required|integer'
            ]);

            if ($validate->fails()) {
                return $this->json(Response::HTTP_BAD_REQUEST, 'invalid param', $validate->getMessageBag());
            }

            return $service->postAsync("/cost", $request->input())
                ->then(
                    function (ResponseInterface $res) use ($request) {
                        $sortparam = $request->get('sort');
                        $data = $this->safeDecode($res->getBody())['rajaongkir'];
                        $this->_filterCourier($data['results']);
                        $this->_filter($data['results']);

                        switch (strtolower($sortparam)) {
                            case 'asc':
                                $output = $this->_combineArray($data['results']);
                                $sorted = collect($output)->sortBy('value');
                                $data = [
                                    'query' => $data['query'],
                                    'status' => $data['status'],
                                    'origin_details' => $data['origin_details'],
                                    'destination_details' => $data['destination_details'],
                                    'results' => $sorted->values()->all()
                                ];
                                break;
                            case  'dsc':
                                $output = $this->_combineArray($data['results']);
                                $sorted = collect($output)->sortByDesc('value');
                                $data = [
                                    'query' => $data['query'],
                                    'status' => $data['status'],
                                    'origin_details' => $data['origin_details'],
                                    'destination_details' => $data['destination_details'],
                                    'results' => $sorted->values()->all()
                                ];
                                break;
                            default :
                                $data['results'] = array_values($data['results']);
                                $data['results'] = $this->_arrayValuesRecursive($data['results']);
                        }
                        return $this->json(Response::HTTP_OK, 'get domestic cost', $data);
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }
                )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    private function _isWahanaCity(Request $request){
        if(strpos($request->input()['destinationType'],'city') !== false && strpos($request->input()['courier'],'wahana') !== false){
            return true;
        }
        else{
            return false;
        }
    }

    private function _wahanaChecker(Request $request, array $data, $sortparam){
        $request_city = new \Illuminate\Http\Request();
        $request_city->merge(['q'=>$data['destination_details']['city_name'],
            'type'=>$data['query']['destinationType']]);
        $result_city = $this->searchCity($request_city);
        $data_city = $this->safeDecode($result_city->content());
        $data_city = $this->_arrayValuesRecursive($data_city['data']['data']);

        foreach ($data_city as $key => &$city){
            if($city['id'] != $data['destination_details']['city_id']){
                unset($data_city[$key]);
            }
            else{
                if($city['priority_region'] != null){
                    $request_data = new \Illuminate\Http\Request();
                    $request_data->setMethod('POST');
                    $request_data->replace(['origin' => $request->input()['origin'],
                        'originType' => $request->input()['originType'],
                        'destination' => $city['priority_region']['id'],
                        'destinationType' => 'subdistrict',
                        'weight' => $request->input()['weight'],
                        'courier' => 'wahana',
                        'length' => $request->input()['length'],
                        'width' => $request->input()['width'],
                        'height' => $request->input()['height'],
                        'sort' => $sortparam
                    ]);
                    $data2 = $this->_getDomesticCostsWahana($request_data);
                    $data_wahana = $this->safeDecode($data2->content());
                    $data_wahana = $this->_arrayValuesRecursive($data_wahana['data']['results']);
                    switch ($sortparam){
                        case 'asc':
                            $data['results'] = array_merge($data['results'], $data_wahana);
                            $sorted = collect($data['results'])->sortBy('value');
                            $data = [
                                'query' => $data['query'],
                                'status' => $data['status'],
                                'origin_details' => $data['origin_details'],
                                'destination_details' => $data['destination_details'],
                                'results' => $sorted->values()->all()
                            ];
                            break;
                        case 'dsc':
                            $data['results'] = array_merge($data['results'], $data_wahana);
                            $sorted = collect($data['results'])->sortByDesc('value');
                            $data = [
                                'query' => $data['query'],
                                'status' => $data['status'],
                                'origin_details' => $data['origin_details'],
                                'destination_details' => $data['destination_details'],
                                'results' => $sorted->values()->all()
                            ];
                            break;
                        default:
                            $data['results'] = array_merge($data['results'], $data_wahana);
                            break;
                    }
                }
            }
        }
        return $data;
    }

    public function getDomesticCosts(Request $request)
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

            $service->setBaseUri(env('GATEWAY_RAJAONGKIR_SERVICE'));

            $service->setHeaders([
                'headers' => [
                    'key' => 'c29168581f10f43d3eede488864a573c'
                ]
            ]);

            $validate = Validator::make($request->input(), [
                'origin' => 'required|integer',
                'originType' => 'required|string',
                'destination' => 'required|integer',
                'destinationType' => 'required|string',
                'weight' => 'required|integer',
                'courier' => 'required|string',
                'length' => 'required|integer',
                'width' => 'required|integer',
                'height' => 'required|integer'
            ]);

            if ($validate->fails()) {
                return $this->json(Response::HTTP_BAD_REQUEST, 'invalid param', $validate->getMessageBag());
            }

            return $service->postAsync("/cost", $request->input())
                ->then(
                    function (ResponseInterface $res) use ($request) {
                        $sortparam = $request->get('sort');
                        $data = $this->safeDecode($res->getBody())['rajaongkir'];
                        $this->_filterCourier($data['results']);
                        $this->_filter($data['results']);
                        switch (strtolower($sortparam)) {
                            case 'asc':
                                $output = $this->_combineArray($data['results']);
                                $sorted = collect($output)->sortBy('value');
                                $data = [
                                    'query' => $data['query'],
                                    'status' => $data['status'],
                                    'origin_details' => $data['origin_details'],
                                    'destination_details' => $data['destination_details'],
                                    'results' => $sorted->values()->all()
                                ];
//                                if($this->_isWahanaCity($request)){
//                                    $data = $this->_wahanaChecker($request, $data, $sortparam);
//                                }
                                break;
                            case  'dsc':
                                $output = $this->_combineArray($data['results']);
                                $sorted = collect($output)->sortByDesc('value');
                                $data = [
                                    'query' => $data['query'],
                                    'status' => $data['status'],
                                    'origin_details' => $data['origin_details'],
                                    'destination_details' => $data['destination_details'],
                                    'results' => $sorted->values()->all()
                                ];
//                                if($this->_isWahanaCity($request)){
//                                    $data = $this->_wahanaChecker($request, $data, $sortparam);
//                                }
                                break;
                            default :
                                $data['results'] = array_values($data['results']);
                                $data['results'] = $this->_arrayValuesRecursive($data['results']);
//                                if($this->_isWahanaCity($request)){
//                                    $data = $this->_wahanaChecker($request, $data, $sortparam);
//                                }
                        }
                        return $this->json(Response::HTTP_OK, 'get domestic cost', $data);
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }
                )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function getInternationalCost()
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

            $service->setBaseUri(env('GATEWAY_RAJAONGKIR_SERVICE'));

            $service->setHeaders([
                'headers' => [
                    'key' => 'c29168581f10f43d3eede488864a573c'
                ]
            ]);

            $validate = Validator::make($this->request->all(), [
                'origin' => 'required|integer',
                'destination' => 'required|integer',
                'weight' => 'required|integer',
                'courier' => 'required|string',
                'length' => 'required|integer',
                'width' => 'required|integer',
                'height' => 'required|integer',
//                'diameter' ')
            ]);

            if ($validate->fails()) {
                return $this->json(Response::HTTP_BAD_REQUEST, 'invalid param', $validate->getMessageBag());
            }
            return $service->postAsync($this->targetUri . "v2/internationalCost", $this->request->all())
                ->then(
                    function (ResponseInterface $res) {
                        return $this->json(Response::HTTP_OK, 'get international cost', $this->safeDecode($res->getBody()));
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }
                )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function searchCity(Request $request)
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
                return $service->getAsync('/pub/search-cities', $request->all())
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

    public function getWaybill(Request $request)
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

            $service->setBaseUri(env('GATEWAY_RAJAONGKIR_SERVICE'));

            $service->setHeaders([
                'headers' => [
                    'key' => 'c29168581f10f43d3eede488864a573c'
                ]
            ]);

            $validate = Validator::make($request->input(), [
                'waybill' => 'required|string',
                'courier' => 'required|string'
            ]);

            if ($validate->fails()) {
                return $this->json(Response::HTTP_BAD_REQUEST, 'invalid param', $validate->getMessageBag());
            }

            return $service->postAsync("/waybill", $request->input())
                ->then(
                    function (ResponseInterface $res) use ($request) {

                        $data = $this->safeDecode($res->getBody())['rajaongkir'];
                        switch ($data['status']['code']){
                            case 200:
                                $sorted = array_reverse(array_sort($data['result']['manifest'], function($value) {
                                    $val = [$value['manifest_date'],
                                        $value['manifest_time']];
                                    return $val;
                                }));
                                $data = [
                                    'query' => $data['query'],
                                    'status' => $data['status'],
                                    'result' => ['delivered' => $data['result']['delivered'],
                                        'summary' => $data['result']['summary'],
                                        'details' => $data['result']['details'],
                                        'delivery_status' => $data['result']['delivery_status'],
                                        'manifest' => $sorted]
                                ];
                                return $this->json(Response::HTTP_OK, 'get waybill details', $data);
//                                return $this->json(Response::HTTP_OK, 'get waybill details', $this->safeDecode($res->getBody())['rajaongkir']);
                                break;
                            case 400:
                                $data['status']['description'] = trim($data['status']['description'], 'Invalid waybill. ');
                                return $this->json(Response::HTTP_BAD_REQUEST, 'get waybill details', $data['status']['description']);
                                break;
                        }
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