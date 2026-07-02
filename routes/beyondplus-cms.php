<?php


/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */


// Route::any('/password/reset','Front\FrontController@index');


Route::group(['prefix' => 'bp-admin','namespace'  =>  'BpAdmin', 'middleware' => 'admins'], function () {

            Route::get('/', 'AdminController@index');
            Route::get('logout','Main@logout');

            // Route::resource('post', 'PostController');

            Route::get('post', 'PostController@index');
            Route::get('post/create', 'PostController@create');
            Route::post('post', 'PostController@store');
            Route::get('post/{id}', 'PostController@show');
            Route::get('post/{id}/edit', 'PostController@edit');
            Route::put('post/{id}', 'PostController@update');
            Route::delete('post/{id}', 'PostController@destroy');


            Route::get('post/{id}/translate', 'PostController@translate');
            Route::get('post/delete/{id}','PostController@destroy');

            Route::get('news', 'NewsController@index');
            Route::get('news/create', 'NewsController@create');
            Route::post('news', 'NewsController@store');
            Route::get('news/{id}', 'NewsController@show');
            Route::get('news/{id}/edit', 'NewsController@edit');
            Route::put('news/{id}', 'NewsController@update');
            Route::delete('news/{id}', 'NewsController@destroy');
            Route::get('news/delete/{id}','NewsController@destroy');

            Route::get('news/{id}/translate', 'NewsController@translate');
            

            // Route::resource('block', 'BlockController');

            Route::get('block', 'BlockController@index');
            Route::get('block/create', 'BlockController@create');
            Route::post('block', 'BlockController@store');
            Route::get('block/{id}', 'BlockController@show');
            Route::get('block/{id}/edit', 'BlockController@edit');
            Route::put('block/{id}', 'BlockController@update');
            Route::delete('block/{id}', 'BlockController@destroy');

            Route::get('block/{id}/translate', 'BlockController@translate');
            Route::get('block/delete/{id}','BlockController@destroy');

            // Route::resource('page', 'PageController');

            Route::get('page', 'PageController@index');
            Route::get('page/create', 'PageController@create');
            Route::post('page', 'PageController@store');
            Route::get('page/{id}', 'PageController@show');
            Route::get('page/{id}/edit', 'PageController@edit');
            Route::put('page/{id}', 'PageController@update');
            Route::delete('page/{id}', 'PageController@destroy');

            Route::get('page/{id}/translate', 'PageController@translate');
            Route::get('page/delete/{id}','PageController@destroy');

            Route::get('user', 'CustomerController@index');
            Route::get('user/create', 'CustomerController@create');
            Route::post('user/store', 'CustomerController@store');
            Route::get('user/{id}', 'CustomerController@show');
            Route::get('user/{id}/edit', 'CustomerController@edit');
            Route::put('user/{id}', 'CustomerController@update');
            Route::delete('user/{id}', 'CustomerController@destroy');

            // Route::resource('user', 'CustomerController');
            Route::get('user/delete/{id}', 'CustomerController@destroy');

            // Route::resource('media', 'MediaController');

            Route::get('media', 'MediaController@index');
            Route::get('media/create', 'MediaController@create');
            Route::post('media', 'MediaController@store');
            Route::get('media/{id}', 'MediaController@show');
            Route::get('media/{id}/edit', 'MediaController@edit');
            Route::put('media/{id}', 'MediaController@update');
            Route::delete('media/{id}', 'MediaController@destroy');

            Route::get('media/delete/{id}','MediaController@destroy');

            // Route::resource('slider', 'SliderController');

            Route::get('slider', 'SliderController@index');
            Route::get('slider/create', 'SliderController@create');
            Route::post('slider/store', 'SliderController@store');
            Route::get('slider/{id}', 'SliderController@show');
            Route::get('slider/{id}/edit', 'SliderController@edit');
            Route::put('slider/{id}', 'SliderController@update');
            Route::delete('slider/{id}', 'SliderController@destroy');

            Route::get('slider/delete/{id}','SliderController@destroy');

            // Route::resource('menu', 'MenuController');

            Route::get('menu', 'MenuController@index');
            Route::get('menu/create', 'MenuController@create');
            Route::post('menu', 'MenuController@store');
            Route::get('menu/{id}', 'MenuController@show');
            Route::get('menu/{id}/edit', 'MenuController@edit');
            Route::put('menu/{id}', 'MenuController@update');
            Route::delete('menu/{id}', 'MenuController@destroy');

            Route::post('permissionupdate', 'PermissionController@ajaxUpdate');
            Route::get('permission','PermissionController@index');
            Route::get('permission/reset', 'PermissionController@permissionReset');

            Route::post('permission', 'PermissionController@ajaxUpdate');

            Route::get('menu/delete/{id}','MenuController@destroy');
            Route::post('menu/pagestore', 'MenuController@pageStore');
            Route::post('menu/poststore', 'MenuController@postStore');
            Route::get('menu/{id}/translate', 'MenuController@translate');

            // Route::resource('category', 'CategoryController');

            Route::get('category', 'CategoryController@index');
            Route::get('category/create', 'CategoryController@create');
            Route::post('category/store', 'CategoryController@store');
            Route::get('category/{id}', 'CategoryController@show');
            Route::get('category/{id}/edit', 'CategoryController@edit');
            Route::put('category/{id}', 'CategoryController@update');
            Route::delete('category/{id}', 'CategoryController@destroy');

            Route::get('category/delete/{id}','CategoryController@destroy');
            Route::get('category/{id}/translate', 'CategoryController@translate');

            Route::get('tax', 'TaxController@index');
            Route::get('tax/add', 'TaxController@create');
            Route::post('tax/add', 'TaxController@store');
            Route::get('tax/{id}', 'TaxController@edit');
            Route::put('tax/{id}','TaxController@update');
            Route::get('tax/delete/{id}','TaxController@destroy');
            Route::get('tax/{id}/translate', 'TaxController@translate');

            Route::get('general','SettingsController@index');
            Route::get('general/add', 'SettingsController@generaledit');
            Route::post('general/add', 'SettingsController@generaledit');

            Route::get('general/modules','ModulesController@index');
            Route::post('general/moduleupdate','ModulesController@ajaxUpdate');

            Route::get('configuration', 'ConfigurationController@index');
            Route::post('configuration', 'ConfigurationController@update');

            // Route::resource('account', 'AccountController');

            Route::get('account', 'AccountController@index');
            Route::get('account/create', 'AccountController@create');
            Route::post('account/store', 'AccountController@store');
            Route::get('account/{id}', 'AccountController@show');
            Route::get('account/{id}/edit', 'AccountController@edit');
            Route::put('account/{id}', 'AccountController@update');
            Route::delete('account/{id}', 'AccountController@destroy');

            Route::get('account/delete/{id}', 'AccountController@destroy');


            // Route::resource('custom', 'CustomController');
            Route::get('custom', 'CustomController@index');
            Route::get('custom/create', 'CustomController@create');
            Route::post('custom/store', 'CustomController@store');
            Route::get('custom/{id}', 'CustomController@show');
            Route::get('custom/{id}/edit', 'CustomController@edit');
            Route::put('custom/{id}', 'CustomController@update');
            Route::delete('custom/{id}', 'CustomController@destroy');

            Route::post('iapi/menu/data', 'Iapi\MenuController@positionChange');

            Route::get('user-guide', 'GuidePageController@index');
            Route::get('user-guide/create', 'GuidePageController@create');
            Route::post('user-guide/store', 'GuidePageController@store');
            Route::get('user-guide/{id}', 'GuidePageController@show');
            Route::get('user-guide/{id}/edit', 'GuidePageController@edit');
            Route::put('user-guide/{id}', 'GuidePageController@update');
            Route::delete('user-guide/{id}', 'GuidePageController@destroy');

            Route::get('user-guide/{id}/translate', 'GuidePageController@translate');
            Route::get('user-guide/delete/{id}','GuidePageController@destroy');


            Route::get('faq', 'FaqController@index');
            Route::get('faq/create', 'FaqController@create');
            Route::post('faq/store', 'FaqController@store');
            Route::get('faq/{id}', 'FaqController@show');
            Route::get('faq/{id}/edit', 'FaqController@edit');
            Route::put('faq/{id}', 'FaqController@update');
           // Route::delete('faq/{id}', 'FaqController@destroy');
            Route::get('faq/delete/{id}','FaqController@destroy');

            


            Route::get('feedback', 'FeedbackController@index');
            Route::get('feedback/create', 'FeedbackController@create');
            Route::post('feedback/store', 'FeedbackController@store');
            Route::get('feedback/{id}', 'FeedbackController@show');
            Route::get('feedback/{id}/edit', 'FeedbackController@edit');
            Route::put('feedback/{id}', 'FeedbackController@update');
            Route::delete('feedback/{id}', 'FeedbackController@destroy');

            Route::get('feedback/delete/{id}','FeedbackController@destroy');


            // Route::get('report/customer', 'reportController@customer');
            Route::get('reports','ReportsController@customerReport');
            Route::get('reports/customer-report-export','ReportsController@customerReportExport');
            Route::get('reports/customer-report','ReportsController@customerReport');

            Route::get('reports/customer-import','ReportsController@customerImportView');
            Route::post('reports/customer-import','ReportsController@customerImport');


            Route::get('reports/sms-report-export','ReportsController@smsReportExport');
            Route::get('reports/sms-report-import','ReportsController@smsReportImport');
            Route::get('reports/sms-report','ReportsController@smsReport');

            
            Route::get('myprofile/edit','MyprofileController@editPassword');
            Route::post('myprofile/edit','MyprofileController@editsavePassword');
            
      });

/**
* Iterate over each language prefix 
*/




      Route::group([ 'middleware' => 'web' ], function () {

            Route::get('/google', 'Front\FrontController@google');
            // 
            // Route::any('/login','Front\FrontController@index');
            // Route::any('/register','Front\FrontController@index');
            // Route::any('/password/reset', 'Front\FrontController@index');

            Route::get('customer/sign-up','Auth\CustomerController@signup');
            Route::post('customer/sign-up', 'Auth\CustomerController@customer_register');

            Route::get('/customer/sign-in','Auth\CustomerController@signin');
            Route::post('/customer/sign-in', 'Auth\CustomerController@login');
            Route::get('/customer/profile', 'Auth\CustomerController@profile');
            Route::get('/customer/logout', 'Auth\CustomerController@logout');


            Route::get('customer/activate', 'Auth\CustomerController@activation');
            Route::post('customer/activate', 'Auth\CustomerController@customer_activation');

            //Route::get('customer/password/reset', 'Auth\CustomerController@passwordreset');

            Route::get('customer/forgot-pass','Auth\CustomerController@forgotpass');
            Route::post('customer/forgot-pass','Auth\CustomerController@post_forgotpass');


            Route::get('customer/new-password','Auth\CustomerController@newPassword');
            Route::post('customer/new-password','Auth\CustomerController@saveNewPassword');

            Route::post('bp-admin/login','BpAdmin\Main@loginAdmin');
            Route::get('bp-admin/login', 'BpAdmin\Main@login');

            // Route::get('syslogin', function() {
            //       return redirect()->to('/bp-admin/login');
            // });

            Route::get('logout','BpAdmin\Main@logout');


            Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');

            Route::get('/open', 'Front\FrontController@openBox');

            Route::post('/feedback', 'Front\FrontController@feedback');
            
            Route::get('/coming-soon', 'Front\FrontController@comingSoon');
            
            Route::get('/', 'Front\FrontController@index');

            Route::get('/home', 'Front\FrontController@index');
            
            // Route::get('/', 'Front\FrontController@redirecttoDashboard');

            Route::get('/sitemap', 'Front\FrontController@sitemap');
            Route::get('/rss', 'Front\FrontController@rss');
            Route::post('/comment', 'Front\FrontController@comment');
            
            Route::get('/{name}', 'Front\FrontController@menu');
            Route::get('/detail/{name}', 'Front\FrontController@post');
            Route::get('/cat/{name}', 'Front\FrontController@cat');

            // Route::auth();

            // Route::get('/test', function(){
            //    return abort(404);
            // });

            Route::get('lang/{lang}', 'Front\FrontController@langChange');

            // Route::get('lang/{lang}', function ($lang) {
            //       if($lang == "mm"){
            //             Session::put('applocale', 'mm');
            //             App::setLocale($locale);
            //       } else {
            //             Session::put('applocale', 'en');
            //             App::setLocale("en");
            //       }    
            //       $lang = App::getLocale();
            //       return redirect()->back();
            // });

            
            Route::get('/news-event/detail/allmeeting', 'Front\FrontController@allMeeting');
            Route::get('/news-event/detail/{post}', 'Front\FrontController@meetingDetail');

            Route::get('/news-event/{lang}', 'Front\FrontController@newsEvent');


      });



