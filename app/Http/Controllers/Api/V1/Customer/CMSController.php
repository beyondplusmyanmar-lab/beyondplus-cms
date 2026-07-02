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
use OpenApi\Attributes as OA;

class CMSController extends Controller
{
    #[OA\Get(
        path: '/api/m/home',
        summary: 'CMS home feed (latest news titles and sliders)',
        tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'Successful operation')]
    )]
    public function index(Request $request)
    {
        $limit = "10";
        $all = Bp_post::limit(5)->where('post_type', 'news')->orderBy('id','desc')->pluck('title')->toArray();
        //$all = ["news 1","d2"];
        $data['update_news'] = implode(". *  ", $all);

        return $data;
    }

    
}