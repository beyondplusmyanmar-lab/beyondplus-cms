<?php

/**
 * Created by Beyond Plus <bplusmyanmar@hotmail.com>
 * User: Beyond Plus
 * Date: D/M/Y
 * Time: MM:HH PM
 */

namespace App\Http\Controllers\Front;

use App;
use App\Http\Controllers\Controller;
use App\Models\Bp_comment;
use App\Models\Bp_menu;
use App\Models\Bp_options;
use App\Models\Bp_post;
use App\Models\Bp_tax;
use App\Models\Faq;
use App\Models\Feedback;
use Carbon\Carbon;
use Google;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class FrontController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->theme = Bp_options::where('option_name', 'theme')->first();
        $this->categories = Bp_tax::where('tax_type', 'category')->get()->all($arrayName = ['tax_name']);
        $this->post_link = Bp_post::select('post_link', 'id')->get();
    }

    public function redirecttoDashboard()
    {
        return redirect()->to('/bp-admin');
    }

    public function t()
    {
        // One responsive theme serves every device; mobile is handled by the
        // separate SPA that consumes the JSON API.
        return $t = 'theme.'.$this->theme->option_value.'.';
    }

    public function template($query, $templateName)
    {
        if ($query === null) {
            abort(404);
        } else {
            $view = ($query->post_template != 'default') ? $query->post_template : $templateName;
            if (view()->exists($this->t().'template/'.$view)) {
                $view = $this->t().'template/'.$view;
            } else {
                $view = $this->t().$templateName;
            }

            return $view;
        }
    }

    public function index()
    {

        return view($this->t().'index', ['title' => 'home',  'categories' => $this->categories, 'post_link' => $this->post_link]);
    }

    public function blog()
    {
        return view($this->t().'blog', ['title' => 'Blog', 'posts' => bp_post(10)]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $posts = null;

        if (mb_strlen($q) >= 2) {
            $posts = Bp_post::whereIn('post_type', ['post', 'page', 'news', 'event'])
                ->where('post_active', 'yes')
                ->where('lang', 1)
                ->where('translate_id', 0)
                ->where(function ($query) use ($q) {
                    $query->where('title', 'like', '%'.$q.'%')
                        ->orWhere('body', 'like', '%'.$q.'%')
                          // Also match the record's translation (e.g. Myanmar
                          // content) so search works in either language.
                        ->orWhereHas('translate', function ($t) use ($q) {
                            $t->where('title', 'like', '%'.$q.'%')
                                ->orWhere('body', 'like', '%'.$q.'%');
                        });
                })
                ->with('translate')
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['q' => $q]);
        }

        return view($this->t().'search', ['title' => 'Search', 'query' => $q ?: null, 'posts' => $posts]);
    }

    public function events(Request $request)
    {
        try {
            $cursor = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Throwable $e) {
            $cursor = Carbon::now()->startOfMonth();
        }

        $events = Bp_post::where('translate_id', 0)
            ->where('post_active', 'yes')
            ->whereNotNull('event_at')
            ->where('event_at', '>=', $cursor->copy()->startOfMonth())
            ->where('event_at', '<', $cursor->copy()->addMonth()->startOfMonth())
            ->with('translate')
            ->orderBy('event_at')
            ->get()
            ->groupBy(fn ($e) => Carbon::parse($e->event_at)->toDateString());

        return view($this->t().'calendar', ['title' => 'Events', 'cursor' => $cursor, 'events' => $events]);
    }

    public function faq()
    {
        if (bp_option('faq_enabled', 'yes') !== 'yes') {
            abort(404);
        }
        $faqs = Faq::where('is_active', 1)->orderBy('sort_order')->orderBy('id')->get();

        return view($this->t().'faq', ['title' => 'FAQ', 'faqs' => $faqs]);
    }

    // Contact = the public feedback form (merged with the old /feedback page).
    public function contact()
    {
        return view($this->t().'contact', ['title' => 'Contact']);
    }

    public function contactStore(Request $request)
    {
        if (bp_option('feedback_enabled', 'yes') !== 'yes') {
            abort(404);
        }

        $msg = app()->getLocale() === 'mm'
            ? 'ကျေးဇူးတင်ပါသည် — သင့်စာကို ပေးပို့ပြီးပါပြီ။'
            : 'Thanks — your message has been sent.';

        // Honeypot: a real user never fills this hidden field. Drop bots silently
        // (pretend success so they don't learn the field is a trap).
        if ($request->filled('website')) {
            return redirect('contact')->with('success', $msg);
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:190',
            'subject' => 'nullable|string|max:190',
            'message' => 'required|string|max:5000',
        ]);
        $feedback = Feedback::create($data);

        // Let plugins react to new feedback (e.g. the Telegram notifier).
        bp_do_action('feedback_received', $feedback);

        return redirect('contact')->with('success', $msg);
    }

    public function menu($name)
    {
        // dd($name);

        $name = urlencode($name);

        // if(\App::getLocale() == "mm"){
        //     $lang = 1;
        // } else {
        //     $lang = 2;
        // }

        // $query = Bp_menu::where('menu_link',$name)->where('lang', $lang)->first();
        $query = Bp_menu::where('menu_link', $name)->first();
        // dd($query);
        if ($query) {
            $bp_post = Bp_post::where('id', $query->post_id)->first();

            $view = $this->template($bp_post, 'single');

            return view($view, ['title' => 'home', 'post' => $bp_post, 'post_link' => $this->post_link]);

        } else {
            return $this->detail($name);
        }
    }

    public function detail($name)
    {
        // dd($name);
        $name = urldecode($name);
        // dd($name);
        $bp_post = Bp_post::with('comment.author')->with('translate')->where('post_link', $name)->first();

        $view = $this->template($bp_post, 'single');

        return view($view, ['title' => 'home', 'post' => $bp_post, 'post_link' => $this->post_link]);
    }

    public function cat($name)
    {
        $cat = Bp_tax::select('tax_id', 'translate_id')->where('tax_type', 'cat')->where('tax_link', $name)->first();

        if (isset($cat)) {

            if ($cat->translate_id == 0) {
                $cat->translate_id = $cat->tax_id;
            }

            $posts = Bp_post::where('translate_id', 0)->whereHas('categories', function ($query) use ($cat) {
                $query->where('bp_relationships.tax_id', $cat->translate_id);
            })->with('translate')->paginate(10);

            return view($this->t().'term', ['title' => 'home', 'posts' => $posts]);

        } else {
            abort(404);
        }

    }

    public function sitemap()
    {
        $posts = Bp_post::latest()->get();

        return response()->view('sitemap', compact(['posts']))->header('Content-Type', 'text/xml');
    }

    public function rss()
    {
        $posts = Bp_post::latest()->limit(20)->get();

        return response()->view('rss', compact(['posts']))->header('Content-Type', 'application/rss+xml');
    }

    // To Do Comment and Search

    public function comment(Request $request)
    {
        // Auth is enforced by the route's "auth" middleware. Calling
        // $this->middleware() here would be a no-op: the middleware stack has
        // already been resolved by the time the action runs.
        $data = $request->validate([
            'post_id' => ['required', 'integer', 'exists:bp_posts,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        // Only these three columns are written. The author comes from the
        // credential, never from the request, and "active" is left to the
        // column default so a commenter cannot set their own moderation state.
        Bp_comment::create([
            'post_id' => $data['post_id'],
            'body' => $data['body'],
            'user_id' => Auth::id(),
        ]);

        // The themes' AJAX handler reloads the page on a literal 1.
        return 1;
    }

    // public function search($q){
    //     $product= Product::where('name','=',$q)->paginate(10);
    //     return view('front.courses' ,$arrayName = array('courses' =>  $product,'mainCategories'=>$this->mainCategories , 'brands' => $this->brands ));
    // }

    public function getClient()
    {
        $client = Google::getClient();
        $client->setApplicationName('Google Sheets API PHP Quickstart');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = 'token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                echo 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(implode(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (! file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    public function google()
    {

        return $client = new Google_Client;

        return $this->getClient();
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $service = new Google_Service_Sheets($client);

        // Prints the names and majors of students in a sample spreadsheet:
        // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
        $spreadsheetId = '1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms';
        $range = 'Class Data!A2:E';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            echo "No data found.\n";
        } else {
            echo "Name, Major:\n";
            foreach ($values as $row) {
                // Print columns A and E, which correspond to indices 0 and 4.
                printf("%s, %s\n", $row[0], $row[4]);
            }
        }

    }

    public function langChange($lang)
    {

        if ($lang == 'mm') {
            Session::put('applocale', 'mm');
            App::setLocale('mm');

            return redirect()->to(url('/'));
        } else {
            Session::put('applocale', 'en');
            App::setLocale('en');

            return redirect()->to(url('/en'));
        }
        // $lang = App::getLocale();
        // dd($lang);
        // return redirect()->to(url('/'));

    }

    public function newsEvent($path)
    {

        // return $path;
        // return $this->t().'template.newsandevents';
        // return $path;
        if ($path == 'news') {
            // return "ok";
            $posts = Bp_post::where('post_type', 'news')->where('translate_id', 0)->with('translate')->orderBy('id', 'desc')->paginate(10);

            return view($this->t().'template.newsandevents', ['title' => 'home', 'posts' => $posts]);
        }

        if ($path == 'event') {
            $posts = [];

            return view($this->t().'template.newsandevents', ['title' => 'home', 'posts' => $posts]);
        }

        if ($path) {
            $name = urlencode($path);
            $bp_post = Bp_post::with('comment.author')->with('translate')->where('post_link', $name)->first();

            // $lang = App::getLocale();

            // if($lang == "mm") {

            $view = $this->template($bp_post, 'single');

            return view($this->t().'template.newsandeventsdetail', ['title' => 'home', 'post' => $bp_post, 'post_link' => $this->post_link]);
        }

    }

    public function departmentDetail($name)
    {
        // return $name;
        // $name = urlencode($name);
        // return substr($name, 0, 4);
        if (substr($name, 0, 4) == 'nal-') {
            $bp_post = Bp_post::with('translate')->where('post_link', $name)->first();

            $view = $this->template($bp_post, 'single');

            return view($this->t().'template.departnalsingledetail', ['title' => 'home', 'post' => $bp_post, 'post_link' => $this->post_link]);

        } else {
            $bp_post = Bp_post::with('translate')->where('post_link', $name)->first();

            $view = $this->template($bp_post, 'single');

            return view($view, ['title' => 'home', 'post' => $bp_post, 'post_link' => $this->post_link]);

        }
        // if(substr(string, 0, 4)$name)

    }

    public function allMeeting()
    {

        //     {
        //     "id": 2243,
        //     "title": "D-7",
        //     "start": "2020-11-29",
        //     "url": "second-agendas/agenda2972020d7",
        //     "color": "#3A87AD",
        //     "feature_image_path": "/front/img/default-photo-thumb.png",
        //     "feature_image_id": "",
        //     "thumb_image_path": "/front/img/default-photo-thumb.png",
        //     "has_media": 0,
        //     "user": null,
        //     "media": [ ]
        // },

        $posts = Bp_post::select('id', 'title', 'event_at as start', 'post_link as url', 'event_color as color', 'featured_img as feature_image_path')->where('post_type', 'event')->where('translate_id', 0)->with('translate')->get();

        return $posts;
    }
}
