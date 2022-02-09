<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Http\Controllers\Base;

use App\Exceptions\AppException;
use Exception;
use Illuminate\Http\Response;
use Yajra\Datatables\Facades\Datatables;

trait RestControllerTrait
{
    protected function _resource()
    {
        return [];
    }

    protected function index()
    {
        if ($this->request->expectsJson()) {
            if ($this->request->get('type') == 'dataTable')
                return Datatables::collection($this->model->get())->make(true);

            $data = $this->model
                ->filter($this->requestMod()['filter_by'], $this->requestMod()['q'])
                ->orderBy(
                    $this->requestMod()['sort_column'],
                    $this->requestMod()['sort_order'])
                ->paginate($this->request->input("per_page"));

            return $this->json(Response::HTTP_OK, 'success', $data);
        }

        return view("$this->rootLayout.$this->layout.index");
    }

    protected function create()
    {
        if ($this->request->expectsJson()) {
            return $this->json(Response::HTTP_OK, 'success', $this->_resource());
        }

        return view("$this->rootLayout.$this->layout.create")->with($this->_resource());
    }

    protected function store()
    {
        try {

            $request = $this->request->input();

            $data = $this->model->storeExec($request);

            if (isset($data->errors) || isset($data->errorInfo)) {
                throw AppException::inst("Save $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            if ($this->request->expectsJson()) {
                return $this->json(Response::HTTP_CREATED, "Save $this->name is successfully.", $data);
            }

            return redirect($this->redirectTo);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function show($id)
    {
        try {
            return $this->json(Response::HTTP_OK, 'success', $this->model->getByIdRef((int)$id)->firstOrFail());

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function edit($id)
    {
        try {
            $data = $this->_resource();
            $data['data'] = $this->model->getByIdRef($id)->firstOrFail();

            if ($this->request->expectsJson()) {

                return $this->json(Response::HTTP_OK, 'success', $data);
            }

            return view("$this->rootLayout.$this->layout.create")->with($data);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function update($id)
    {
        try {
            $request = $this->request->input();

            $data = $this->model->updateExec($request, $id);

            if (isset($data->errors) || isset($data->errorInfo)) {
                throw AppException::inst("update $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            if ($this->request->expectsJson()) {
                return $this->json(Response::HTTP_CREATED, "$this->name updated.", $data);
            }

            return redirect($this->redirectTo);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function destroy($id)
    {
        try {
            $data = $this->model->destroyExec($id);

            if (isset($data->errors)) {
                throw AppException::inst("delete $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            return $this->json(Response::HTTP_OK, "delete $this->name is successfully.", $data);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function list()
    {
        $data = $this->model->get();
        return $this->json(
            Response::HTTP_OK,
            'success',
            $data);
    }
}
