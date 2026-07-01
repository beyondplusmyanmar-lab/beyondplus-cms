<?php

namespace App\Services;

use DB;
use File;
use Auth;
use App\Services\XmlJsonService;

class OtapiService
{
    public $xmlJsonService;
    public $otabase_url;
    public $instance_key;

    public function __construct(XmlJsonService $xmlJsonService){
        $this->xmlJsonService = $xmlJsonService;
        $this->otabase_url          =  "http://otapi.net/service-json";
        $this->instance_key         =   "162bbe5a-ef4d-406e-8c8d-6c95a5a05e44";
        //customer
        $this->session_id           =   "6595ad4b-b153-490d-bf8a-37beaf5a205b";
        // $this->session_id           =   "90ac9633-f551-40db-932a-1d0d54c250fe";
        // $this->session_id           =   "6595ad4b-b153-490d-bf8a-37beaf5a205b";
        //root session
        $this->op_session_id        =   "8491b342-6425-4184-a5fe-99dae64febfb";
        $this->staff_op_session_id  =   "a696acda-763e-4d1c-ba53-d22c3b4c9810";
        
    }


     //category test
        // $data = $this->otapiService->getSearchCategoryInfoList();
        // $data = $data['CategoryInfoList']['Content'];
        // $data = $this->categoryService->minicategoryJsonFormat($data, 'multiple');
        // $this->categoryRepo->save_category($data);
        // return  $data;
        
    public function getSearchCategoryInfoList() {

        $url =  $this->otabase_url ."/GetSearchCategoryInfoList?instanceKey=".$this->instance_key."&language=en";

        //$url =  $this->otabase_url ."/GetItemInfo?instanceKey=opendemo&language=en&itemId=".$id;
        $path = storage_path('app/public/upload/json/');
        $download_status = false;
        $file = "";

        return $this->xmlJsonService->json_download($url, $file, $path, $download_status);
    }


    public function getBatchsearchlist(){
        //header('Content-Type: application/json;  charset=utf-8');
        $data = File::get(storage_path('app/public/upload/json/batchsearchlist.json'));

      //  return $data;
      return $data = json_decode($data, true);

      //return json_encode(json_decode($data, true), true);
    }

    public function getProductDetail($id) {
        // 560724366248
        // abb-560724366248 

        // http://otapi.net/service-json/GetItemFullInfoWithPromotions?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&itemParameters=&itemId=abb-560724366248 

        $url =  $this->otabase_url ."/GetItemFullInfoWithPromotions?instanceKey=".$this->instance_key."&language=en&itemId=".$id;



       
        $path = storage_path('app/public/upload/json/');
        $download_status = true;
        $file = "productdetail.json";

        $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);


        if($product === Null) {
            $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);
        }

        return $product;
    }

    public function getProductDetailDescription($id){

        $url =  $this->otabase_url ."/GetItemOriginalDescription?instanceKey=".$this->instance_key."&language=en&itemId=".$id;



       
        $path = storage_path('app/public/upload/json/');
        $download_status = true;
        $file = "productdetail.json";

        $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);


        if($product === Null) {
            $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);
        }

        return $product;

    }

    public function findCategoryItemInfoListFrame($catid) {
        http://otapi.net/service-json/FindCategoryItemInfoListFrame?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&categoryId=3035&categoryItemFilter=%3CSearchParameters%3E+++%3CCategoryId%3E3035%3C%2FCategoryId%3E++%3C%2FSearchParameters%3E&framePosition=0&frameSize=10
        $rand = rand(0,100);
        $url =  $this->otabase_url ."/FindCategoryItemInfoListFrame?instanceKey=".$this->instance_key."&language=en&categoryId=".$catid."&categoryItemFilter=%3CSearchParameters%3E+++%3CCategoryId%3E".$catid."%3C%2FCategoryId%3E++%3C%2FSearchParameters%3E&framePosition=".$rand."&frameSize=8";



       
        $path = storage_path('app/public/upload/json/');
        $download_status = true;
        $file = "productdetail.json";

        $response = $this->xmlJsonService->json_download($url, $file, $path, $download_status);


        if($response === Null) {
            $response = $this->xmlJsonService->json_download($url, $file, $path, $download_status);
        }

        return $response;

    }


    public function getCategoryDetail($id, $position) {

        // http://otapi.net/service-json/BatchSearchItemsFrame?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en
        // &sessionId=godMode&framePosition=0&frameSize=2&blockList=&xmlParameters=<SearchItemsParameters><CategoryId>otc-110</CategoryId></SearchItemsParameters>
        // http://otapi.net/service-json/BatchSearchItemsFrame?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&sessionId=godMode&framePosition=0&frameSize=2&blockList=&xmlParameters=<SearchItemsParameters><CategoryId>otc-110</CategoryId></SearchItemsParameters>

        $url =  $this->otabase_url ."/BatchSearchItemsFrame?instanceKey=".$this->instance_key."&language=en&sessionId=".$this->staff_op_session_id."&framePosition=0&frameSize=40&blockList=&xmlParameters=<SearchItemsParameters><CategoryId>".$id."</CategoryId></SearchItemsParameters>";




       
        $path = storage_path('app/public/upload/json/');
        $download_status = true;
        $file = "categorydetail.json";

        $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);


        if($product === Null) {
            $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);
        }

        return $product;
    }

    public function batchImageSearchFrame() {

        // http://otapi.net/service-json/BatchSearchItemsFrame?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&sessionId=a696acda-763e-4d1c-ba53-d22c3b4c9810&xmlParameters=%3CSearchItemsParameters%3E+++%3CProvider%3ETaobao%3C%2FProvider%3E+++%3CImageUrl%3Ehttps%3A%2F%2Fwww.google.ru%2Fimages%2Fbranding%2Fgooglelogo%2F2x%2Fgooglelogo_color_272x92dp.png%3C%2FImageUrl%3E+%3C%2FSearchItemsParameters%3E&framePosition=0&frameSize=40&blockList=
    }

    public function batchSearchItemsFrame($name, $framePosition = 0, $provider = "Taobao") {

        $url =  $this->otabase_url ."/BatchSearchItemsFrame?instanceKey=".$this->instance_key."&language=en&sessionId=".$this->staff_op_session_id."&framePosition=".$framePosition."&frameSize=20&blockList=AvailableSearchMethods&xmlParameters=<SearchItemsParameters><Provider>".$provider."</Provider><ItemTitle>".$name."</ItemTitle></SearchItemsParameters>";

        // $path = storage_path('app/public/upload/json/');
        // $download_status = true;
        // $file = "batchsearchlist.json";


        // http://otapi.net/service-json/BatchSearchItemsFrame?instanceKey=f867ebf2-219f-471a-81b8-078b434a1a78&language=en&sessionId=godMode&framePosition=0&frameSize=2&blockList=AvailableSearchMethods&xmlParameters=%3CSearchItemsParameters%3E%0D%0A++%3CItemTitle%3ECats%3C%2FItemTitle%3E%0D%0A%3C%2FSearchItemsParameters%3E

        // $url =  $this->otabase_url ."/BatchSearchRatingLists?instanceKey=".$this->instance_key."&language=en&applicationType=&xmlSearchParameters=%3CBatchRatingListSearchParameters%3E%20%3CRatingLists%3E%20%3CRatingList%3E%20%3CCategoryId%3E0%3C/CategoryId%3E%20%3CItemRatingType%3ELast%3C/ItemRatingType%3E%20%3CContentType%3EItem%3C/ContentType%3E%20%3CFramePosition%3E0%3C/FramePosition%3E%20%3CFrameSize%3E18%3C/FrameSize%3E%20%3C/RatingList%3E%3C/RatingLists%3E%20%3CUseDefaultParameters%3Efalse%3C/UseDefaultParameters%3E%20%3C/BatchRatingListSearchParameters%3E";


        $path = storage_path('app/public/upload/json/');
        $download_status = true;
        $file = "batchkeywordsearchlist.json";

        $product= $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        if($product === Null) {
            $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);
        }

        return $product;
    }

    public function batchSearchItemsFrameForImage($pathurl, $framePosition = 0 , $provider = "Taobao") {

        // Taobao
        // Warehouse
        // YahooJapan
        // YahooJapanAuction
        // Kitmall
        // Alibaba1688
        // if(substr($q,0,4) == "http") {
            $url =  $this->otabase_url ."/BatchSearchItemsFrame?instanceKey=".$this->instance_key."&language=en&sessionId=".$this->staff_op_session_id."&framePosition=".$framePosition."&frameSize=40&blockList=AvailableSearchMethods&xmlParameters=<SearchItemsParameters><Provider>".$provider."</Provider><ImageUrl>".$pathurl."</ImageUrl></SearchItemsParameters>";
        // } else {
        //     $url =  $this->otabase_url ."/BatchSearchItemsFrame?instanceKey=".$this->instance_key."&language=en&sessionId=".$this->staff_op_session_id."&framePosition=".$framePosition."&frameSize=20&blockList=AvailableSearchMethods&xmlParameters=<SearchItemsParameters><Provider>".$provider."</Provider><ImageUrl>".$pathurl."</ImageUrl></SearchItemsParameters>";
        // }

        

        $path = storage_path('app/public/upload/json/');
        $download_status = true;
        $file = "batchkeywordsearchlist.json";

        $product= $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        if($product === Null) {
            $product = $this->xmlJsonService->json_download($url, $file, $path, $download_status);
        }

        return $product;
    }
    

    // category

    public function setBatchsearchlist(){
      // $data = json_encode(['Text 11','Text 2','Text 3','Text 4','Text 5']);
      // $file = time() . '_file.json';
      // $destinationPath= storage_path('app/public')."/upload/json/";
      // if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
      // File::put($destinationPath.$file,$data);
      //return Storage::disk("public/upload/json/")->put($file, $data);
      //return response()->download($destinationPath.$file);
      $url =  $this->otabase_url ."/BatchSearchRatingLists?instanceKey=".$this->instance_key."&language=en&applicationType=&xmlSearchParameters=%3CBatchRatingListSearchParameters%3E%20%3CRatingLists%3E%20%3CRatingList%3E%20%3CCategoryId%3E0%3C/CategoryId%3E%20%3CItemRatingType%3ELast%3C/ItemRatingType%3E%20%3CContentType%3EItem%3C/ContentType%3E%20%3CFramePosition%3E0%3C/FramePosition%3E%20%3CFrameSize%3E18%3C/FrameSize%3E%20%3C/RatingList%3E%3C/RatingLists%3E%20%3CUseDefaultParameters%3Efalse%3C/UseDefaultParameters%3E%20%3C/BatchRatingListSearchParameters%3E";
      $path = storage_path('app/public/upload/json/');
      $download_status = true;
      $file = "batchsearchlist.json";

      $this->xmlJsonService->json_download($url, $file, $path, $download_status);
    }

    // public function xmlFormatter() {
    //     $xmlParams = new \SimpleXMLElement('<Fields></Fields>');
    //     $promoid = null;
    //     $ItemURL = "http://asdf";
    //     $comment = "ok";
    //     $deliveryMode = false;

    //     $el = $xmlParams->addChild('FieldInfo');
    //     $el->addAttribute('Name', 'PromoId');
    //     $el->addAttribute('Value', $promoid);

    //     if ($deliveryMode) {
    //         $el = $xmlParams->addChild('FieldInfo');
    //         $el->addAttribute('Name', 'ExternalDeliveryId');
    //         $el->addAttribute('Value', $deliveryMode);
    //     }

    //     $el = $xmlParams->addChild('FieldInfo');
    //     $el->addAttribute('Name', 'ItemURL');
    //     $el->addAttribute('Value', $ItemURL);

    //     $el = $xmlParams->addChild('FieldInfo');
    //     $el->addAttribute('Name', 'Comment');
    //     $el->addAttribute('Value', htmlspecialchars($comment));

    //     // echo $el->asXML();

    //     return response($xmlParams->asXML(), 200)->header('Content-Type', 'application/xml');

    // }

    function clearBasket() {

        $sessionId          = $this->session_id;
        // $deliveryModeId     = "EMS";
        // 1014b3 COD
        $deliveryModeId     = "1014b3";
        
        // $comment            = urlencode($comment);
        $weight             = "1";

        $url =  $this->otabase_url ."/ClearBasket?instanceKey=".$this->instance_key."&language=en&sessionId=".$sessionId;
        //echo $url;

        $path = storage_path('app/public/upload/json/');
        $download_status = false;
        $file = "";

        return $data = $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        if($data['ErrorCode'] == "Ok") {
            echo "CreateSalesOrder";
            echo "";
        }

    }

    function batchSimplifiedAddItemsToBasket($itemData = [], $comment) {

        $comment            = urlencode($comment);

        // Add to basket
        // echo $this->CreateSalesOrder(1,"test");
        // die();
        $xmlParams = new \SimpleXMLElement('<Request></Request>');
        $promoid = null;
        // $ItemURL = "http://asdf";
        $comment = "ok";
        $deliveryMode = false;

        
        $childXmlParams = $xmlParams->addChild('Element');
        // product_id
        $childXmlParams->addAttribute('ItemId', $itemData['product_id']);
        $childXmlParams->addAttribute('ItemURL', $itemData['taobao_item_url']);
        $childXmlParams->addAttribute('ExternalDeliveryId', 'EMS');
        // ItemId="629627663943" ItemURL="https://item.taobao.com/item.htm?id=629627663943" ExternalDeliveryId="EMS"

            $el = $childXmlParams->addChild('Current');
            $el->addAttribute('ConfigurationId', $itemData['ConfiguredItem']['Id']);
            $el->addAttribute('Quantity', $itemData['quantity'] );

            if( count($itemData['ConfiguredItem']['Configurators']) > 0) {

                foreach ($itemData['ConfiguredItem']['Configurators'] as $key => $Configurators) {
                    
                    $elchild = $el->addChild('Property');
                    $elchild->addAttribute('Id', $Configurators['Pid']);
                    $elchild->addAttribute('ValueId',$Configurators['Vid']);
                }
            }

               

                // $elchild = $el->addChild('Property');
                // $elchild->addAttribute('Id', '1627207');
                // $elchild->addAttribute('ValueId', '4053551');

            // if ($deliveryMode) {
            //     $el = $childXmlParams->addChild('FieldInfo');
            //     $el->addAttribute('Name', 'ExternalDeliveryId');
            //     $el->addAttribute('Value', $deliveryMode);
            // }

            $elSelected = $childXmlParams->addChild('Selected');
            $elSelected->addAttribute('ConfigurationId', $itemData['ConfiguredItem']['Id']);
            $elSelected->addAttribute('Quantity', $itemData['quantity'] );

            if( count($itemData['ConfiguredItem']['Configurators']) > 0) {

                foreach ($itemData['ConfiguredItem']['Configurators'] as $key => $Configurators) {
                    
                    $elchild = $elSelected->addChild('Property');
                    $elchild->addAttribute('Id', $Configurators['Pid']);
                    $elchild->addAttribute('ValueId',$Configurators['Vid']);
                }
            }

                // $elchild = $elSelected->addChild('Property');
                // $elchild->addAttribute('Id', '20509');
                // $elchild->addAttribute('ValueId', '3727387');

                // $elchild = $elSelected->addChild('Property');
                // $elchild->addAttribute('Id', '1627207');
                // $elchild->addAttribute('ValueId', '4053551');


        $xmlpara = $xmlParams->asXML();
        

        $xmlpara = urlencode(str_replace('<?xml version="1.0"?>',"",$xmlpara));

        //----------------------------

        $sessionId = $this->session_id;

        $url =  $this->otabase_url ."/BatchSimplifiedAddItemsToBasket?instanceKey=".$this->instance_key."&language=en&sessionId=".$sessionId."&xmlRequest=".$xmlpara;

        $path = storage_path('app/public/upload/json/');
        $download_status = false;
        $file = "";

        return $data = $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        // echo json_encode($data);

        // if($data['ErrorCode'] == "Ok") {

        //     $data = $this->CreateSalesOrder(1,"test");

        //     echo json_encode($data);
        //     //dd($this->CreateSalesOrder(1,"test"));
        //     echo "CreateSalesOrder";
        //     echo "";
        // }


    }


    function CreateSalesOrder($sessionId,$comment) {
        // $sessionId = "6e5f50b2-5eca-4775-aba7-af0d7f5ba049";
        $sessionId          = $this->session_id;
        // $deliveryModeId     = "EMS";
        // 1014b3 COD
        $deliveryModeId     = "1014b3";
        
        $comment            = urlencode($comment);
        $weight             = "1";

        $url =  $this->otabase_url ."/CreateSalesOrder?instanceKey=".$this->instance_key."&language=en&sessionId=".$sessionId."&deliveryModeId=".$deliveryModeId."&comment=".$comment."&weight=".$weight;
        //echo $url;

        $path = storage_path('app/public/upload/json/');
        $download_status = false;
        $file = "";

        return $data = $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        if($data['ErrorCode'] == "Ok") {
            echo "CreateSalesOrder";
            echo "";
        }
    }

    function GetSalesOrderDetails($orderid) {
        // http://otapi.net/OtapiWebService2.asmx/GetSalesOrderDetails?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&sessionId=a6b962ef-f471-4ec4-893b-2bcbb87741e2&salesId=ORD-0000000020
        $sessionId          = $this->session_id;
        // $deliveryModeId     = "EMS";
        // 1014b3 COD
        $deliveryModeId     = "1014b3";
        
        // $comment            = urlencode($comment);
        $weight             = "1";

        $url =  $this->otabase_url ."/GetSalesOrderDetails?instanceKey=".$this->instance_key."&language=en&sessionId=".$sessionId."&salesId=".$orderid;
        //echo $url;

        $path = storage_path('app/public/upload/json/');
        $download_status = false;
        $file = "";

        return $data = $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        if($data['ErrorCode'] == "Ok") {
            echo "CreateSalesOrder";
            echo "";
        }

    }

    //order detail
    function GetSalesOrderDetailsForOperator($orderid) {

        $sessionId          = $this->op_session_id;
        // $deliveryModeId     = "EMS";
        // 1014b3 COD
        $deliveryModeId     = "1014b3";
        
        // $comment            = urlencode($comment);
        $weight             = "1";

        // $url =  $this->otabase_url ."/GetSalesOrderDetailsForOperator?instanceKey=".$this->instance_key."&language=en&sessionId=".$sessionId."&salesId=".$orderid."&filter=&queryType=1";
        $url =  $this->otabase_url ."/GetSalesOrderDetailsForOperator?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&sessionId=".$sessionId."&salesId=".$orderid."&filter=&queryType=1";
        
        // http://otapi.net/service-json/GetSalesOrderDetailsForOperator?instanceKey=162bbe5a-ef4d-406e-8c8d-6c95a5a05e44&language=en&sessionId=8491b342-6425-4184-a5fe-99dae64febfb&salesId=ORD-0000000021&filter=&queryType=1
        //echo $url;

        $path = storage_path('app/public/upload/json/');
        $download_status = false;
        $file = "";

        return $data = $this->xmlJsonService->json_download($url, $file, $path, $download_status);

        if($data['ErrorCode'] == "Ok") {
            echo "CreateSalesOrder";
            echo "";
        }

    }



}