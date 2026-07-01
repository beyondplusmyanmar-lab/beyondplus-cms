<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;

use Hash;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Http\Resources\Admin\PlayerResource;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;
use Carbon\Carbon;
use App\Models\Bp_post;

class CMSController extends Controller
{
      /**
     * @SWG\Get(
     *   path="/player",
     *   summary="Sample",
     *   @SWG\Response(response=200, description="successful operation")
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $limit = "10";
        $all = Bp_post::limit(5)->where('post_type', 'news')->orderBy('id','desc')->pluck('title')->toArray();
        //$all = ["news 1","d2"];
        $data['update_news'] = implode(". *  ", $all);

        return $data;
    }

    
}