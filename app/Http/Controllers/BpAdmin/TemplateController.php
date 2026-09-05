<?php

namespace App\Http\Controllers\BpAdmin;

use App\Http\Controllers\Controller;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function service()
    {
        return view('theme/compound/layouts/service');
    }
}
