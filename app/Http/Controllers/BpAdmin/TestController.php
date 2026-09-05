<?php

namespace App\Http\Controllers\BpAdmin;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function custom()
    {
        return view('custom/test');
    }
}
