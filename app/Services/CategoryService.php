<?php

namespace App\Services;

use DB;
use Auth;
use Hash;
use Session;

use App\Models\Category;

class CategoryService
{

    // public function productJsonFormat($data, $type) {

    //     $data = $data;
    //     $data['Price'] = $data['Price'];
        

    //     return json_encode($data);
    // }


    public function minicategoryJsonFormat($input, $type) {

        if($type == 'multiple') {
            foreach ($input as $key => $data) {
                $minidata[$key]['oid']            = $data['Id'];
                $minidata[$key]['provider_type']  = $data['ProviderType'];
                $minidata[$key]['ishidden']       = $data['IsHidden'];
                $minidata[$key]['isvirtual']      = $data['IsVirtual'];
                $minidata[$key]['externalid']     = $data['ExternalId'];
                $minidata[$key]['name']           = $data['Name'];
                $minidata[$key]['isparent']       = $data['IsParent'];
                $minidata[$key]['parentid']       = (isset($data['ParentId'])) ? $data['ParentId'] : "";
                $minidata[$key]['isinternal']     = $data['IsInternal'];
                $minidata[$key]['dash']           = 1;
                $minidata[$key]['active']         = 1;
            }
        } else {
            $minidata['oid']            = $input['Id'];
            $minidata['provider_type']  = $input['ProviderType'];
            $minidata['ishidden']       = $input['IsHidden'];
            $minidata['isvirtual']      = $input['IsVirtual'];
            $minidata['externalid']     = $input['ExternalId'];
            $minidata['name']           = $input['Name'];
            $minidata['isparent']       = (isset($data['ParentId'])) ? $data['ParentId'] : "";
            $minidata['parentid']       = $input['ParentId'];
            $minidata['isinternal']     = $input['IsInternal'];
            $minidata['dash']           = 1;
            $minidata['active']         = 1;
        }
        
        
        

        return $minidata;
    }


}