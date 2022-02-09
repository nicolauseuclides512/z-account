<?php

namespace App\Http\Controllers\Gateway\Asset;

use App\Http\Requests\Regions\SearchCityRequest;
use App\Services\Gateway\Base\BaseServiceContract;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Http\Message\ResponseInterface;

class AssetRegionGateController extends AssetGateController
{
    protected $targetUri = '/regions';

    public function __construct(BaseServiceContract $service, Request $request)
    {
        parent::__construct($service, $request);
    }

    public function getByDistrict($id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/districts/{id}", ['id' => $id])
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

    public function searchCity(SearchCityRequest $request)
    {
        try {

            $minChar = $request->get('min_char') ?? 3;

            if (strlen($request->get('q')) >= $minChar) {
                return $this->service->getAsync(
                    $this->targetUri . '/search-cities',
                    $request->all())
                    ->then(
                        function (ResponseInterface $res) {
                            return $this->jsonGzSuccess($res);
                        },
                        function (RequestException $e) {
                            return $this->jsonExceptions($e);
                        }
                    )->wait();
            }

            return $this->json(
                Response::HTTP_OK,
                'get city list',
                []);

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

}
