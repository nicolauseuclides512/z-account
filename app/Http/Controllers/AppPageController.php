<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class AppPageController extends Controller
{
    public function message()
    {
        return view('pages.message');
    }
}
