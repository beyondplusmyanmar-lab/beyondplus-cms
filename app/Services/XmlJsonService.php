<?php

namespace App\Services;

use DB;
use File;
use Auth;

class XmlJsonService
{

    function json_download($Url, $file, $destinationPath, $download_status){
         
            // is cURL installed yet?
            if (!function_exists('curl_init')){
                die('Sorry cURL is not installed!');
            }
         
            // OK cool - then let's create a new cURL resource handle
            $ch = curl_init();
         
            // Now set some options (most are optional)
         
            // Set URL to download
            curl_setopt($ch, CURLOPT_URL, $Url);
         
            // Include header in result? (0 = yes, 1 = no)
            curl_setopt($ch, CURLOPT_HEADER, 0);
         
            // Should cURL return or print out the data? (true = return, false = print)
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         
            // Timeout in seconds
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            // Set Data Decompression
            curl_setopt($ch,CURLOPT_ENCODING, '');
         
            // Download the given URL, and return output
            $output = curl_exec($ch);

            if($download_status) {
                $destinationPath= storage_path('app/public/upload/json/');

                if (!is_dir($destinationPath)) {  
                    mkdir($destinationPath,0777,true);  
                }

                File::put($destinationPath.$file,$output);

                return json_decode($output,true);
                // /return true;
            }

            // $xml = new SimpleXMLElement($output);
            // $converter = new XmlToJsonConverter();
            // $jsonArray = $converter->convert($xml);

            
            curl_close($ch);

            return json_decode($output,true);


    }

    function curl_download($Url,$name){
         
            // is cURL installed yet?
            if (!function_exists('curl_init')){
                die('Sorry cURL is not installed!');
            }
         
            // OK cool - then let's create a new cURL resource handle
            $ch = curl_init();
         
            // Now set some options (most are optional)
         
            // Set URL to download
            curl_setopt($ch, CURLOPT_URL, $Url);
         
            // Include header in result? (0 = yes, 1 = no)
            curl_setopt($ch, CURLOPT_HEADER, 0);
         
            // Should cURL return or print out the data? (true = return, false = print)
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         
            // Timeout in seconds
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            // Set Data Decompression
            curl_setopt($ch,CURLOPT_ENCODING, '');
         
            // Download the given URL, and return output
            $output = curl_exec($ch);

            $xml = new SimpleXMLElement($output);

    		// $parseObj = str_replace('xs:',"",$file);
    		// $xml= simplexml_load_string($parseObj);

    		$converter = new XmlToJsonConverter();
    		$jsonArray = $converter->convert($xml);

    		
    		// $parseObj = str_replace('xs:',"",$file);
    		// $xml= simplexml_load_string($parseObj);
            // $output = str_replace("@", '', $output);

            // Close the cURL resource, and free system resources
            curl_close($ch);

            return $jsonArray;

            // Save data to local xml file
            // $file = fopen(BASEPATH.'libraries/Cache/'.$name.'.xml', 'w');
            // fwrite($file, $jsonArray);
            // fclose($file);

    }
}