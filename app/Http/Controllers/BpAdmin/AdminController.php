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
        $post = Bp_post::whereNotIn('post_type',['post','event','news','page','user-guide'])->orderBy('updated_at','desc')->paginate(20);

        // $post = Bp_post::where('post_type','post')->orderBy('created_at','DESC')->paginate(5);
        $totalPost= $post->total();
        $allUser=Customers::paginate(5)->total();

        $latestUsers= Customers::orderBy('created_at','DESC')->paginate(12);
        return view('bp-admin.home', array('post' => $post , 'allUser' => $allUser, 'latestUsers' => $latestUsers ,'totalPost' => $totalPost));
    }



}
