<?php

namespace App\Http\Controllers\BpAdmin\Iapi;

use App\Http\Controllers\Controller;
use App\Models\Bp_menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public $categories;

    public function __construct()
    {
        $this->middleware('admins');
    }

    public function positionChange(Request $request)
    {
        $parent_id = $request->input('parent_id');
        $menu_id = $request->input('menu_id');
        $weight = $request->input('weight');

        Bp_menu::where('menu_id', $menu_id)->update(['parent_id' => $parent_id]);

        if (isset($weight)) {
            $weight = explode(',', $weight);
            foreach ($weight as $key => $w) {
                Bp_menu::where('menu_id', $w)->update(['menu_weight' => $key]);
            }
        }

        return $request->all();
        // return view('bp-admin.menu.index', array('menu' => $this->menu, 'pages' => $this->pages, 'posts' => $this->posts));
    }
}
