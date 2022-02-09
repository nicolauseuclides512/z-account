<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Cores;


use App\Utils\DateTimeUtil;
use Illuminate\Support\Facades\Storage;

class Image
{

    public static function removeObject($key)
    {
        if (Storage::disk('s3')->exists($key)) {
            return (Storage::disk('s3')->delete($key)) ? true : false;
        }
        return false;
    }

    public function removeObjectBulk($keys = array())
    {
        return $s3 = Storage::disk('s3')->delete($keys);
    }

    public function copyBulk($keys)
    {
        array_map(function ($k) {
            if (strpos($k, env('S3_URL')) !== false) {
                $clearK = str_replace(env('S3_URL'), '', $k);
                Storage::disk('s3')->copy('tmp/' . $clearK, $clearK);
            }
        }, $keys);
    }

    public static function removeTemp()
    {
        Storage::disk('s3')->deleteDirectory('temp');
    }

    public static function generatePath($keyId, $folder, $isUsingTemp = false)
    {
        $rootPath = '';

        if ($isUsingTemp) $rootPath = 'tmp/';

        $orgKey = sha1('52b202878635bd08534c58b0d7' . $keyId);

        return $rootPath . "$orgKey/$folder/" . bin2hex(random_bytes(7)) . DateTimeUtil::currentMicroSecond();
    }

}