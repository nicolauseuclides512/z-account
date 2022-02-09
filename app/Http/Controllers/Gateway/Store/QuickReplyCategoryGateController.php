<?php

namespace App\Http\Controllers\Gateway\Store;

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */


class QuickReplyCategoryGateController extends StoreGateController
{
    protected $targetUri = '/quick_reply_category';
}