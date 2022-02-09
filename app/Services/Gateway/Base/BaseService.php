<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Services\Gateway\Base;

use GuzzleHttp\Client;

abstract class BaseService implements BaseServiceContract
{
    use BaseServicePresenterTrait;

    protected $client;

    protected $baseUri;

    protected $targetUri;

    protected $headers = [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
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

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}