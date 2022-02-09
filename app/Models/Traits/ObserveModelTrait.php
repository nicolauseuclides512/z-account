<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Models\Traits;

use App\Models\Base\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait ObserveModelTrait
{
    public static function bootObservable()
    {
        static::saved(function (BaseModel $model) {
            $model->created_by = 'anonymous';

            if (Auth::User())
                $model->created_by = Auth::User()->email;

            if ($model::$autoValidate) {
                $validation = Validator::make($model->attributes, $model::rules($model->id));

                #cheking validation
                if ($validation->fails()) {
                    $model->errors = $validation->messages()->all();
                    return false;
                } else {
                    return true;
                }
            }
        });

        static::saving(function (BaseModel $model) {

            $model->created_by = 'anonymous';

            if (Auth::User())
                $model->created_by = Auth::User()->email;

            if ($model::$autoValidate) {
                $validation = Validator::make($model->attributes, $model::rules($model->id));

                #cheking validation
                if ($validation->fails()) {
                    $model->errors = $validation->messages()->all();
                    return false;
                } else {
                    return true;
                }
            }
        });

        static::updated(function (BaseModel $model) {
            $model->updated_by = 'anonymous';

            if (Auth::User())
                $model->updated_by = Auth::User()->email;

        });

        static::updating(function (BaseModel $model) {
            $model->updated_by = 'anonymous';

            if (Auth::User())
                $model->updated_by = Auth::User()->email;

        });

        static::deleted(function (BaseModel $model) {
            $model->deleted_by = 'anonymous';

            if (Auth::User())
                $model->deleted_by = Auth::User()->email;

        });

        static::deleting(function (BaseModel $model) {
            $model->deleted_by = 'anonymous';

            if (Auth::User())
                $model->deleted_by = Auth::User()->email;

            foreach ($model->getSoftCascades() as $relation) {
                foreach ($model->{$relation} as $item) {
                    $item->delete();
                }
            }
        });
    }
}