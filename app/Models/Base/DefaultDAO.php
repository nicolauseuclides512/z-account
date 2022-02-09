<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Models\base;


use App\Exceptions\AppException;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

trait DefaultDAO
{
    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public function storeExec($request)
    {
        DB::beginTransaction();
        try {
            $request['action'] = 'store';
            $data = $this->populate($request);
            if (!$data->save()) {
                DB::rollback();
                throw AppException::flash(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    "Update data Failed.",
                    $data->errors
                );
            }
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param $request
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function updateExec($request, $id)
    {
        DB::beginTransaction();
        try {
            $request['action'] = 'update';
            $dataInId = $this->getByIdRef($id)->firstOrFail();
            $data = $this->populate($request, $dataInId);
            if (!$data->save()) {
                DB::rollback();
                if (isset($data->errors))
                    return $data->errors;
                else
                    throw AppException::inst(
                        "Updating data failed."
                    );
            }
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function destroyExec($id)
    {
        DB::beginTransaction();
        try {
            $request['action'] = 'destroy';
            $dataInId = $this->getByIdRef($id)->firstOrFail();
            if (!$dataInId->delete()) {
                DB::rollback();
                throw AppException::inst(
                    "Deleting data failed."
                );
            }
            DB::commit();
            return $dataInId;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function destroySomeExec($ids)
    {
        $data = array_map(function ($id) {
            $dataInId = $this->getByIdRef($id)->first();
            if (!empty($dataInId)) {
                return array('errors' => "data by id $id not found");
            }
            if (!$dataInId->delete()) {
                return $dataInId;
            }
            return $dataInId;
        }, explode(',', preg_replace('/\s+/', '', $ids)));


        return $data;
    }

    public function markAsExec($ids, $status)
    {
        // TODO: Implement markAsExec() method.
    }
}