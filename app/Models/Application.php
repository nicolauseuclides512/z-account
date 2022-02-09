<?php

namespace App\Models;

use App\Exceptions\AppException;
use Illuminate\Http\Response;

class Application extends MasterModel
{
    protected $table = 'applications';

    protected $fillable = [
        'name',
    ];

    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|no_space',
        ];
    }

    public static function scopeFindByName($q, $name)
    {
        return $q->where('name', $name);
    }

    public static function scopeFindByIdOrName($q, $name)
    {
        $data = $q->where('id', $name) ?? $q->where('name', $name);

        return $data->first();
    }

    /**
     * @param $data
     * @throws AppException
     */
    public static function fillApplicationId(&$data)
    {
        if (isset($data['application'])) {
            $app = self::findByName($data['application'])->first();
            if (!$app) {
                throw AppException::inst(
                    'Invalid application specified',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        } else {
            $app = self::orderBy('id', 'asc')->first();
            if (!$app) {
                throw AppException::inst(
                    'Application is not setup yet',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }

        $data['application_id'] = $app->id;
    }

}
