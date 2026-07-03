<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Bp_post;
use App\Models\Bp_slider;
use App\Models\Bp_menu;
use App\Models\Bp_tax;
use OpenApi\Attributes as OA;

/**
 * Public, read-only CMS content API (/api/m/*).
 *
 * These endpoints are unauthenticated but rate limited by the "api" middleware
 * group (throttle:60,1 per IP). Only published content is exposed, page sizes
 * are capped, and responses expose a curated set of fields.
 */
class CMSController extends Controller
{
    /** Hard cap on page size — prevents resource-exhaustion via huge per_page. */
    private const MAX_PER_PAGE = 50;

    // ---- helpers ---------------------------------------------------------

    private function respond($data, int $status = 200, array $extra = [])
    {
        return response()->json(array_merge(['status' => $status, 'data' => $data], $extra), $status);
    }

    private function notFound(string $message)
    {
        return $this->respond(['message' => $message], 404);
    }

    /** Requested locale, whitelisted to 'en' or 'mm' (default). */
    private function lang(Request $request): string
    {
        return $request->query('lang') === 'en' ? 'en' : 'mm';
    }

    /** Use the translated record when the English locale is requested. */
    private function loc($model, string $lang)
    {
        return ($lang === 'en' && $model && $model->translate) ? $model->translate : $model;
    }

    private function perPage(Request $request): int
    {
        return max(1, min((int) $request->query('per_page', 10), self::MAX_PER_PAGE));
    }

    private function meta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
        ];
    }

    private function image(?string $file): ?string
    {
        return $file ? url('uploads/'.$file) : null;
    }

    private function postCard($post, string $lang): array
    {
        $t = $this->loc($post, $lang);
        return [
            'id'      => $post->id,
            'title'   => $t->title,
            'slug'    => $post->post_link,
            'excerpt' => Str::limit(trim(strip_tags(preg_replace('/\[block\].*?\[\/block\]/is', '', (string) $t->body))), 160),
            'image'   => $this->image($post->featured_img),
            'date'    => optional($post->created_at)->toDateString(),
        ];
    }

    private function postDetail($post, string $lang): array
    {
        $t = $this->loc($post, $lang);
        return [
            'id'         => $post->id,
            'title'      => $t->title,
            'slug'       => $post->post_link,
            'body'       => bbParse($t->body),
            'image'      => $this->image($post->featured_img),
            'date'       => optional($post->created_at)->toDateString(),
            'categories' => $post->categories->map(fn ($c) => ['name' => $c->tax_name, 'slug' => $c->tax_link])->values(),
        ];
    }

    private function menuNode($m, string $lang): array
    {
        $t = $this->loc($m, $lang);
        return [
            'title'    => $t->menu_name,
            'url'      => $m->menu_type === 'default' ? '/'.ltrim((string) $m->menu_link, '/') : $m->menu_link,
            'type'     => $m->menu_type,
            'children' => collect($m->children ?? [])->where('lang', 1)
                            ->map(fn ($c) => $this->menuNode($c, $lang))->values(),
        ];
    }

    /** Base query for published, base-language content of a given type. */
    private function publishedPosts(string $type)
    {
        return Bp_post::where('post_type', $type)
            ->where('post_active', 'yes')
            ->where('lang', 1)
            ->where('translate_id', 0)
            ->with('translate')
            ->orderBy('id', 'desc');
    }

    /** Published news and events (both post types), newest first. */
    private function publishedNews()
    {
        return Bp_post::whereIn('post_type', ['news', 'event'])
            ->where('post_active', 'yes')
            ->where('lang', 1)
            ->where('translate_id', 0)
            ->with('translate')
            ->orderBy('id', 'desc');
    }

    private function newsCard($post, string $lang): array
    {
        $card = $this->postCard($post, $lang);
        $card['type'] = $post->post_type;      // "news" or "event"
        $card['event_at'] = $post->event_at;   // set for events
        return $card;
    }

    private function sliderList()
    {
        return Bp_slider::orderBy('slider_weight')->get()->map(fn ($s) => [
            'id'          => $s->slider_id,
            'title'       => $s->slider_name,
            'description' => $s->slider_description,
            'image'       => $this->image($s->slider_link),
        ]);
    }

    // ---- endpoints -------------------------------------------------------

    #[OA\Get(path: '/api/m/home', summary: 'Home feed: site info, sliders, latest posts and news', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function home(Request $request)
    {
        $lang = $this->lang($request);

        return $this->respond([
            'site' => [
                'name'        => optional(site_information('blogname'))->option_value,
                'description' => optional(site_information('blogdescription'))->option_value,
            ],
            'sliders'      => $this->sliderList(),
            'latest_posts' => $this->publishedPosts('post')->limit(6)->get()->map(fn ($p) => $this->postCard($p, $lang)),
            'news'         => $this->publishedNews()->limit(5)->get()->map(fn ($p) => $this->newsCard($p, $lang)),
        ]);
    }

    #[OA\Get(path: '/api/m/posts', summary: 'Paginated list of published posts', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function posts(Request $request)
    {
        $lang = $this->lang($request);
        $page = $this->publishedPosts('post')->paginate($this->perPage($request));

        return $this->respond(
            collect($page->items())->map(fn ($p) => $this->postCard($p, $lang)),
            200,
            ['meta' => $this->meta($page)]
        );
    }

    #[OA\Get(path: '/api/m/posts/{slug}', summary: 'Single published post by slug', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function post(Request $request, string $slug)
    {
        $post = $this->publishedPosts('post')->where('post_link', $slug)->with('categories')->first();

        return $post ? $this->respond($this->postDetail($post, $this->lang($request)))
                     : $this->notFound('Post not found.');
    }

    #[OA\Get(path: '/api/m/pages', summary: 'List of published pages', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function pages(Request $request)
    {
        $lang = $this->lang($request);

        return $this->respond(
            $this->publishedPosts('page')->reorder('id', 'asc')->get()
                ->map(fn ($p) => ['title' => $this->loc($p, $lang)->title, 'slug' => $p->post_link])
        );
    }

    #[OA\Get(path: '/api/m/pages/{slug}', summary: 'Single published page by slug', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function page(Request $request, string $slug)
    {
        $page = $this->publishedPosts('page')->where('post_link', $slug)->with('categories')->first();

        return $page ? $this->respond($this->postDetail($page, $this->lang($request)))
                     : $this->notFound('Page not found.');
    }

    #[OA\Get(path: '/api/m/menus', summary: 'Navigation menu tree', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function menus(Request $request)
    {
        $lang = $this->lang($request);
        $menus = Bp_menu::where('lang', 1)->where('parent_id', 0)
            ->with('children', 'translate')->orderBy('menu_weight')->get();

        return $this->respond($menus->map(fn ($m) => $this->menuNode($m, $lang))->values());
    }

    #[OA\Get(path: '/api/m/categories', summary: 'List of content categories', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function categories(Request $request)
    {
        return $this->respond(
            Bp_tax::where('tax_type', 'cat')->where('lang', 1)->orderBy('tax_name')->get()
                ->map(fn ($c) => ['name' => $c->tax_name, 'slug' => $c->tax_link])
        );
    }

    #[OA\Get(path: '/api/m/categories/{slug}/posts', summary: 'Published posts in a category', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Not found')])]
    public function categoryPosts(Request $request, string $slug)
    {
        $cat = Bp_tax::where('tax_type', 'cat')->where('tax_link', $slug)->first();
        if (! $cat) {
            return $this->notFound('Category not found.');
        }

        $lang = $this->lang($request);
        $page = Bp_post::where('post_active', 'yes')->where('translate_id', 0)
            ->whereHas('categories', fn ($q) => $q->where('bp_relationships.tax_id', $cat->tax_id))
            ->with('translate')->orderBy('id', 'desc')->paginate($this->perPage($request));

        return $this->respond(
            collect($page->items())->map(fn ($p) => $this->postCard($p, $lang)),
            200,
            ['meta' => $this->meta($page)]
        );
    }

    #[OA\Get(path: '/api/m/sliders', summary: 'Homepage sliders', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function sliders(Request $request)
    {
        return $this->respond($this->sliderList());
    }

    #[OA\Get(path: '/api/m/news', summary: 'Paginated news and events', tags: ['CMS'],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function news(Request $request)
    {
        $lang = $this->lang($request);
        $page = $this->publishedNews()->paginate($this->perPage($request));

        return $this->respond(
            collect($page->items())->map(fn ($p) => $this->newsCard($p, $lang)),
            200,
            ['meta' => $this->meta($page)]
        );
    }

    #[OA\Get(path: '/api/m/search', summary: 'Search published posts and pages by title or body', tags: ['CMS'],
        parameters: [new OA\Parameter(name: 'q', in: 'query', required: true, schema: new OA\Schema(type: 'string', minLength: 2))],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Require a minimum term length to avoid overly broad / expensive scans.
        if (mb_strlen($q) < 2) {
            return $this->respond([], 200, ['meta' => ['query' => $q, 'total' => 0]]);
        }

        $lang = $this->lang($request);
        // The like value is bound as a parameter — safe from SQL injection.
        $page = Bp_post::whereIn('post_type', ['post', 'page'])
            ->where('post_active', 'yes')
            ->where('lang', 1)
            ->where('translate_id', 0)
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', '%'.$q.'%')
                      ->orWhere('body', 'like', '%'.$q.'%');
            })
            ->with('translate')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage($request));

        return $this->respond(
            collect($page->items())->map(function ($p) use ($lang) {
                $card = $this->postCard($p, $lang);
                $card['type'] = $p->post_type;   // "post" or "page"
                return $card;
            }),
            200,
            ['meta' => array_merge(['query' => $q], $this->meta($page))]
        );
    }
}
