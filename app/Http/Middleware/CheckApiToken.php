<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Apikeytable;
use Auth;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $segment = "m";

        if($request->segment(2)) {
                $segment = $request->segment(2);
        }

        if($segment == "m") {
            return $next($request);
        } 
        elseif($segment == "sportsbook")  {
            return $next($request);
        }
        elseif($segment == "socialviber")  {
            return $next($request);
        }
        else {

            if ($request->hasHeader('X-Trident-Token')) {
                // echo $request->header('X-Trident-Token');
                if(!empty(trim($request->header('X-Trident-Token')))){

                    $XTridentToken = $request->header('X-Trident-Token');

                    $is_exists = Apikeytable::where('api_token' , $XTridentToken)->exists();

                    if($is_exists){
                        return $next($request);
                    } else {
                        return response()->json([ "data" => [ "status" => 401] ], 401);
                    }
                }
            }
            
        }
        // dd($segment);
// /api/m
        

        // die();
        //     if(!empty(trim($request->input('api_token')))){

        //         $is_exists = Apikeytable::where('id' , Auth::guard('api')->id())->exists();

        //         if($is_exists){
        //             return $next($request);
        //         } else {
        //             return response()->json('Invalid Token', 401);
        //         }
        //     }
           return response()->json([ "data" => [ "status" => 401] ], 401);
    }
}
