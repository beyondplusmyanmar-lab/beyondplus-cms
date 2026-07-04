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
use App\Models\Bp_tax;
use App\Models\Bp_term;
use App\Models\Bp_post;
use App\Models\Bp_relationship;
use App\Models\User;
use Auth;
use Validator;

class NewsController extends Controller
{
    var $categories;
    public function __construct()
    {
       $this->middleware('admins');
       $this->taxes =  Bp_tax::where('tax_type','cat')->get();
    }

    public function index(){
        $post = Bp_post::whereIn('post_type',['news','event'])->orderBy('updated_at','desc')->where('translate_id',0)->paginate(13);
        return view('bp-admin.news.index', array('post' => $post));
    }

    /** Month calendar of dated events (post.event_at). */
    public function calendar(Request $request){
        try {
            $cursor = $request->filled('month')
                ? \Carbon\Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
                : \Carbon\Carbon::now()->startOfMonth();
        } catch (\Throwable $e) {
            $cursor = \Carbon\Carbon::now()->startOfMonth();
        }

        $events = Bp_post::where('translate_id', 0)
            ->whereNotNull('event_at')
            ->where('event_at', '>=', $cursor->copy()->startOfMonth())
            ->where('event_at', '<', $cursor->copy()->addMonth()->startOfMonth())
            ->orderBy('event_at')
            ->get()
            ->groupBy(fn ($e) => \Carbon\Carbon::parse($e->event_at)->toDateString());

        return view('bp-admin.news.calendar', compact('cursor', 'events'));
    }

    public function create(){
       return view('bp-admin.news.add', array('taxes' => $this->taxes));

    }

    public function store(Request $request){
        bp_validate_images($request, ['featured_img']);

        $validator = Validator::make($request->all(), [
            'title' => 'required', 
            'body' => 'required',
        ]);

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inputs = $request->all();
        $inputs['post_link'] = formatUrl($request->input('title'));
        $inputs['post_type'] = $request->input('post_type');
        if($request->input('post_type') == "event") {
            $inputs['event_at'] = $request->filled('event_at')
                ? \Carbon\Carbon::parse($request->input('event_at'))->toDateTimeString()
                : null;
        }
        
        $inputs['post_created'] = Auth::guard('admins')->user()->id;
        Bp_post::create($inputs);

        $update_id = Bp_post::orderBy('id', 'desc')->first();
        
        if ($__up = bp_store_image($request->file('featured_img'), 'feat')) {
            $inputs['featured_img'] = $__up;
        }

        $categories  = $request->get('taxes');

        $this->termInsert($categories,$update_id->id);

        return redirect()->to('bp-admin/news');
    }

    public function edit($id)
    {
        try {
            $post = Bp_post::findOrFail($id);
            $tax_type = Bp_relationship::where('post_id',$id)->where('type','cat')->pluck('tax_id')->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.news.edit', array('post' => $post, 'taxes' => $this->taxes, 'tax_type' => $tax_type));

    }

    public function update($id, Request $request)
    {
        bp_validate_images($request, ['featured_img']);
        $inputs = $request->all();
        $inputs['post_link'] = formatUrl($request->input('title'));
        $inputs['post_type'] = $request->input('post_type');
        if($request->input('post_type') == "event") {
            $inputs['event_at'] = $request->filled('event_at')
                ? \Carbon\Carbon::parse($request->input('event_at'))->toDateTimeString()
                : null;
        }
        
        if ($__up = bp_store_image($request->file('featured_img'), 'feat')) {
            $inputs['featured_img'] = $__up;
        }

        Bp_post::findOrFail($id)->update($inputs);

        $categories  = $request->get('taxes');

        //Deleteing Term
        $this->termInsert($categories,$id);

        return redirect()->to('bp-admin/news');
    }

    public function destroy($id)
    {
        $news = Bp_post::find($id);
        if ($news) {
            bp_delete_upload($news->featured_img);
            $news->delete();
        }
        return redirect()->back();
    }

    public function termInsert($categories,$id) {
        if($categories){
            if(sizeof($categories)>0){
                Bp_relationship::where('post_id',$id)->where('type','cat')->delete();
            }
            //Recreating New Term
            for( $i=0; $i<sizeof($categories); $i++){
                $cat['tax_id'] = $categories[$i];
                $cat['post_id'] = $id;
                $cat['type']    = 'cat';
                Bp_relationship::create($cat);
            }
        } else {
            $cat['tax_id'] = 1;
            $cat['post_id'] = $id;
            $cat['type']    = 'cat';
            Bp_relationship::create($cat);
        }
    }


    public function translate($id) {
        try {
            $post = Bp_post::findOrFail($id);
            $tax_type = Bp_relationship::where('post_id',$id)->where('type','post')->pluck('tax_id')->toArray();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return 'Post Not Found';
        }
        return view('bp-admin.news.translate', array('post' => $post, 'taxes' => $this->taxes, 'tax_type' => $tax_type,'translate_id' => $id));
    }
}
