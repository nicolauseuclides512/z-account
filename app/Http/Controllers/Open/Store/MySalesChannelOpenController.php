<?php

namespace App\Http\Controllers\Open\Store;

use App\Cores\Jsonable;
use App\Cores\TokenGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class MySalesChannelOpenController extends Controller
{

    use Jsonable;

    private $url, $header;

    public function _init(Request $request)
    {

        $this->url = env('GATEWAY_STORE_SERVICE') . '/open';
        $this->header = [
            'headers' => [
                'Authorization' => 'Bearer ' . TokenGenerator::inst()->createToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-localization' => $request->hasHeader('X-localization')
                    ? $request->header('X-localization')
                    : 'id',
                'X-Header-Organization-Id' => Auth::user()->getCurrentOrganization()->id
            ]];

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function fetch(Request $request)
    {
        try {
            $this->_init($request);

            $sort = explode('.', $request->get('sort'));
            $sortColumn = $sort[0] ?? '';
            $sortOrder = $sort[1] ?? '';
            $filter = $request->get('filter') ?? 'all';

            $parameters =
                array_merge(
                    $this->header,
                    $request->all(),
                    [
                        'sort' => $sortColumn . '.' . $sortOrder,
                        'filter' => $filter,
                    ]);

            $promise = [
                'myChannels' => app(Client::class)
                    ->getAsync(
                        $this->url . '/my-sales-channels',
                        $parameters
                    )
            ];

            $res = Promise\unwrap($promise);

            return $this->jsonGzSuccess($res['myChannels']);

        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

}
