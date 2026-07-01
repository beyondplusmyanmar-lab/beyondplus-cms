<?php

namespace App\Services;

use DB;
use File;

class GoogleTranslateService
{
    function google_translate($target_lang,$text){
        $data   = [];

        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=".$target_lang."&dt=t&q=".$text;

        $options = array(
          'http'=>array(
            'method'=>"GET",
            'header'=>"Accept-language: en\r\n" .
                      "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                      "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
          )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // $result = file_get_contents();
        $arr = explode('"',$result);
        $data[0]= $arr[1];
        $data[1]= $arr[3];
        return $data;
    }

}