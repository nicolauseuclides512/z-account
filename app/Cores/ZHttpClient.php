<?php
/**
 * @author Jehan Afwazi Ahmad <jehan.afwazi@gmail.com>.
 */


namespace App\Cores;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ZHttpClient extends Client
{
    private $url;

    private $header;

    public static function init($url, array $header = [])
    {
        $me = new self();

        $me->url = $url;
        $me->header = ['headers' => array_merge(
            ['Content-Type' => 'application/json'],
            $header
        )];

        return $me;
    }

    /**
     * @param $targetUrl
     * @return mixed
     */
    public function url($targetUrl)
    {
        $url = $this->url . "/$targetUrl";
        Log::info('Http Request to ' . $url);
        return $url;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     * @return ZHttpClient
     */
    public function setHeader($header): ZHttpClient
    {
        $this->header = $header;
        return $this;
    }


}