<?php

namespace App\Http\Controllers;

use App\Cores\Jsonable;
use App\Http\Controllers\Base\BaseController;
use Illuminate\Http\Response;

class HomeController extends BaseController
{
    use Jsonable;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function langData()
    {
        //TODO(ekli): pilih salah satu metode kalo udah ntar hapus yang tidak dipake
        $jsonData = json_decode(file_get_contents(storage_path('/app/lang/lang.json')));

        $arrayData = include storage_path('/app/lang/lang.php');
        return $this->json(
            Response::HTTP_OK,
            'fetch lang data',
            $arrayData
        );
    }

}
