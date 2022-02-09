<?php

namespace App\Http\Controllers\Gateway\Store;

use App\Exceptions\AppException;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */
class ContactGateController extends StoreGateController
{
    protected $targetUri = '/contacts';

    public function importData()
    {
        try {

            $file = $this->request->file('raw_file');

            if (is_null($file))
                throw AppException::inst("FIle not found.");

            $fileUrl = Storage::disk('s3')->temporaryUrl($file, Carbon::now()->addHour(1));

            $this->request->request->add(['raw_file' => $fileUrl]);

            return $this->service->postAsync(
                $this->targetUri . "/import-data",
                $this->request->input()
            )->then(
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