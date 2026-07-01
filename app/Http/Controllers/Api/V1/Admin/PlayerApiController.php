<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Http\Resources\Admin\PlayerResource;
use App\Models\Customers;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class PlayerApiController extends Controller
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

    public function index()
    {
        //abort_if(Gate::denies('project_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PlayerResource(Customers::get());
    }

    public function store(Request $request)
    {
        // return $request;
        $output['data']['status'] = "403";
        $output['data']['message'] = [];

        $validator = Validator::make($request->all(), [
                    'phone' => 'required|unique:customers,phone,',
                    'name'  => 'required',
                    'dob'   => 'required',
                ]);


        // return $validated = $request->validate([
        //     'phone' => 'required|unique:customers|max:255'
        // ]);

        if ($validator->fails()) { 

            $output['data']['message'] =  "Please check Necessary Fields";
             // array_push($output['message'], "Please check Field");

            return $output;
        }

        $data =  $request->all();

        if(isset($data['name']) && isset($data['dob']) ) {
            $data['first_name'] = $data['name'];
            $data['date_of_birth'] = $data['dob'];

            if($data['mail_status'] == 1) {
                $data['email'] = $data['mail'];
                unset($data['mail']);
            }
            
            unset($data['name']);
            unset($data['dob']);
            

            $project = Customers::create($data);

            return (new PlayerResource($project))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } else {
            // return "Please check field";
            // array_push($output['message'], ["Please check Field"]);
            $output['data']['message'] =  "Please check Necessary Field";

            return $output;
        }
        
    }

    
    public function show(Customers $project)
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PlayerResource($project->load(['author']));
    }

    
    public function update(UpdatePlayerRequest $request, Project $project)
    {
        $project->update($request->all());

        return (new PlayerResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    
    public function destroy(Project $project)
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}