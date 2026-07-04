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
            Route::get('news/calendar', 'NewsController@calendar');
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
            // Admin language switch — sets the session locale, returns to the page.
            Route::get('lang/{lang}', function ($lang) {
                $l = $lang === 'mm' ? 'mm' : 'en';
                session(['applocale' => $l]);
                app()->setLocale($l);
                return redirect()->back();
            });
            Route::get('themes', 'ThemeController@index');
            Route::get('themes/scan', 'ThemeController@scan');
            Route::post('themes/activate', 'ThemeController@activate');
            Route::get('plugins', 'PluginController@index');
            Route::get('plugins/view', 'PluginController@show');
            Route::get('plugins/scan', 'PluginController@scan');
            Route::get('plugins/settings', 'PluginController@settings');
            Route::post('plugins/settings', 'PluginController@saveSettings');
            Route::post('plugins/test', 'PluginController@test');
            Route::post('plugins/activate', 'PluginController@activate');
            Route::post('plugins/update', 'PluginController@update');
            Route::post('plugins/deactivate', 'PluginController@deactivate');
            Route::post('plugins/uninstall', 'PluginController@uninstall');

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
            Route::get('feedback/delete/{id}', 'FeedbackController@destroy');
            Route::get('feedback/{id}', 'FeedbackController@show');


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




      Route::group([ 'middleware' => ['web', 'frontend.mode'] ], function () {

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

            // Hardened login: when a secret path is configured it becomes the real
            // login, and bp-admin/login above turns into a decoy (see Main).
            try {
                $__loginPath = trim((string) bp_option('admin_login_path', ''), '/');
                if ($__loginPath !== '' && $__loginPath !== 'login') {
                    Route::get('bp-admin/'.$__loginPath, 'BpAdmin\Main@login');
                    Route::post('bp-admin/'.$__loginPath, 'BpAdmin\Main@loginAdmin');
                }
            } catch (\Throwable $__e) { /* DB not ready */ }

            // Route::get('syslogin', function() {
            //       return redirect()->to('/bp-admin/login');
            // });

            Route::get('logout','BpAdmin\Main@logout');


            Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');

            Route::get('/open', 'Front\FrontController@openBox');

            Route::get('/coming-soon', 'Front\FrontController@comingSoon');
            
            Route::get('/', 'Front\FrontController@index');

            Route::get('/home', 'Front\FrontController@index');
            
            // Route::get('/', 'Front\FrontController@redirecttoDashboard');

            Route::get('/sitemap', 'Front\FrontController@sitemap');
            Route::get('/rss', 'Front\FrontController@rss');
            Route::post('/comment', 'Front\FrontController@comment');

            Route::get('/blog', 'Front\FrontController@blog');

            // Public events calendar.
            Route::get('/events', 'Front\FrontController@events');

            // FAQ (404s unless enabled) + the merged Contact / feedback page.
            Route::get('/faq', 'Front\FrontController@faq');
            Route::get('/contact', 'Front\FrontController@contact');
            Route::post('/contact', 'Front\FrontController@contactStore')->middleware('throttle:5,1');
            // Old /feedback URLs now point at the Contact page.
            Route::get('/feedback', function () { return redirect('/contact'); });
            Route::post('/feedback', 'Front\FrontController@contactStore')->middleware('throttle:5,1');

            // Preview an error page without forcing a real failure. Dev/admin only:
            // works when APP_DEBUG is on, or for a signed-in admin. e.g.
            // /preview-error/404, /preview-error/500 (the 500 also shows the dev log).
            Route::get('/preview-error/{code}', function ($code) {
                abort_unless(config('app.debug') || auth()->guard('admins')->check(), 404);
                abort_unless(in_array($code, ['401', '403', '404', '419', '429', '500', '503'], true), 404);

                $data = $code === '500'
                    ? ['exception' => new \Symfony\Component\HttpKernel\Exception\HttpException(
                        500, '', new \RuntimeException('Simulated error for preview'))]
                    : [];

                return response()->view("errors.{$code}", $data, (int) $code);
            })->where('code', '[0-9]+');

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



