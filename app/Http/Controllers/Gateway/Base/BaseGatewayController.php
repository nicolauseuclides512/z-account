<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Http\Controllers\Gateway\Base;

use App\Cores\Jsonable;
use App\Cores\TokenGenerator;
use App\Exceptions\AppException;
use App\Services\Gateway\Base\BaseServiceContract;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

abstract class BaseGatewayController extends Controller
{
    use RestGateControllerTrait, Jsonable;

    protected $isStrict = true;

    protected $service;

    protected $baseUri;

    protected $targetUri;

    protected $request;

    public function __construct(BaseServiceContract $service, Request $request, $config = null)
    {
        $this->service = $service;
        $this->request = $request;
        $this->service->setBaseUri($this->baseUri);
        $this->service->setTargetUri($this->targetUri);

        if ($this->isStrict) {
            $this->middleware(function (Request $request, $next) {
                $organizationId = (int)$request->header('X-Header-Organization-Id');
                $token = TokenGenerator::inst()
                    ->create($organizationId,
                        $request->bearerToken());

                $this->service->setHeaders([
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'x-localization' => $request->hasHeader('X-localization')
                            ? $request->header('X-localization')
                            : 'id',
                        'X-Header-Organization-Id' => $organizationId,
                    ]
                ]);

                return $next($request);
            });
        }
    }

    public function getService(BaseServiceContract $service)
    {
        $this->service = $service;
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public function getTargetUri()
    {
        return $this->targetUri;
    }

    public function setTargetUri($targetUri)
    {
        $this->targetUri = $targetUri;
    }
}
