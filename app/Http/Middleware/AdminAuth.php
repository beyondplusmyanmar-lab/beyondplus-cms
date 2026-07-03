<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Bp_module;
use App\Models\Bp_access;


class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'admins')
    {

        if (Auth::guard($guard)->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('bp-admin/login');
            }
        }

        if ($request->method() === 'GET') {
            
            

            if($request->segment(2)) {
                $segment = $request->segment(2);

                if($segment == "logout") {
                     return $next($request);
                }


                if($segment == "myprofile") {
                    return $next($request);
                }

                if($segment == "lang") {
                    return $next($request);
                }
                
                // dd($segment);

                // return bp_module::where('parent_id',0)->where('section',1)->with('child')->get();
                $modules = Bp_module::get()->pluck('module_link','module_id')->toArray();
                //module check

                // dd($modules);
                // return array_values($modules);
                $role = Auth::guard("admins")->user()->role;

                
                // dd(role_type($role));

                if (in_array($segment, array_values($modules))) {

                    $module_id = array_search($segment, $modules);
                    // $role_type = role_type($role);

                    $access = Bp_access::where('module_id', $module_id )->where('usertype', $role  )->first();

                    if($access->canshow == 0) {
                        return response('Unauthorized.', 401);
                    }

                    return $next($request);

                } else {
                    return response('Unauthorized.', 401);
                }

            }

            

        }

        return $next($request);
        
    }
}
