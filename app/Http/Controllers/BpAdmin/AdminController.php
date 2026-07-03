<?php
/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */
namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Bp_post;
use App\Models\Bp_media;
use App\Admin;
use App\User;
use App\Models\Customers;

class AdminController extends Controller
{
    public function __construct()
    {
       $this->middleware('admins');
    }

    public function index()
    {
        $post = Bp_post::where('post_type', 'post')->orderBy('updated_at', 'desc')->limit(6)->get();
        $totalPost = Bp_post::where('post_type', 'post')->count();
        $totalPage = Bp_post::where('post_type', 'page')->count();
        $totalMedia = Bp_media::count();
        $allUser = Customers::count();

        $latestUsers= Customers::orderBy('created_at','DESC')->paginate(12);
        return view('bp-admin.home', array('post' => $post , 'allUser' => $allUser, 'latestUsers' => $latestUsers ,'totalPost' => $totalPost, 'totalPage' => $totalPage, 'totalMedia' => $totalMedia));
    }



}
