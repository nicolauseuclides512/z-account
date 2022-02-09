<?php

namespace App\Http\Controllers\Base;

use App\Cores\Filter;
use App\Cores\Jsonable;
use App\Cores\Sort;
use App\Models\base\BaseModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, RestControllerTrait, Jsonable;

    protected $redirectTo = '/';

    protected $rootLayout = '';

    protected $layout = '';

    protected $model;

    protected $name;

    protected $request;

    protected $statusColumn = '';

    protected $sortBy = ['created_at'];

    protected $validIndexParams = [];

    protected $validStoreParams = [];

    protected $validMarkAsParams = [];

    protected function __construct(BaseModel $modelName = null, Request $request = null)
    {
        $this->model = $modelName;
        $this->request = $request;

        if (empty($this->validIndexParams))
            $this->validIndexParams = ["page", "per_page", "sort", "filter", "q"];
    }

    protected function requestMod()
    {
        $req = $this->request;

        $page = $req->input('page') ?? 1;
        $perPage = $req->input('per_page') ?? env('APP_PER_PAGE', 15);
        $sortColumn = 'created_at';
        $sortOrder = Sort::ASC;
        $filterByRaw = 'all';
        $q = $req->get('q') ?? '';

        if (!empty($req->input('sort'))) {
            $sort = explode('.', $req->input('sort'));
            $sortColumn = Sort::getColumn($sort[0], $this->sortBy);
            $sortOrder = Sort::getOrder(strtoupper($sort[1]));
        }

        #filter
        $filterValue = Filter::getFilter($req->input('filter') ?? $filterByRaw, $this->model->filterCfg());
        $filterBy = $this->model->filterCfg()[$filterValue];

        #return
        $newRequest = array(
            'page' => $page,
            'sort_column' => $sortColumn,
            'sort_order' => $sortOrder,
            'filter_by' => $filterBy,
            'per_page' => $perPage,
            'q' => strtolower($q)
        );

        $newQuery = $req->input();
        $newQuery['sort'] = $sortColumn . '.' . strtolower($sortOrder);
        $newQuery['filter'] = $filterByRaw;
        $newQuery['per_page'] = $perPage;
        $newQuery['q'] = $q;

        $query = [];
        foreach ($newQuery as $key => $value) {
            if ($key != 'page')
                $query[] = $key . '=' . $value;
        }
        $query = '&' . implode('&', $query);

        $newRequest['query'] = $query;

        return $newRequest;
    }

}
