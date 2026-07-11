<?php

namespace Tests\Feature;

use App\Models\Bp_options;
use App\Models\Bp_post;
use App\Models\Bp_tax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Beyond Plus CMS');
    }

    public function test_admin_login_page_loads(): void
    {
        $this->get('/bp-admin/login')->assertStatus(200)->assertSee('Sign in', false);
    }

    public function test_search_finds_matching_content(): void
    {
        $this->get('/search?q=Multilingual')->assertStatus(200)->assertSee('Building Multilingual Content');
        $this->get('/search?q=a')->assertStatus(200); // too short: prompt, no error
    }

    public function test_events_calendar_loads(): void
    {
        $this->get('/events')->assertStatus(200);
        $this->get('/events?month=2026-08')->assertStatus(200);
    }

    public function test_api_home_returns_json_envelope(): void
    {
        $this->getJson('/api/m/home')
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }

    public function test_api_search_and_404(): void
    {
        $this->getJson('/api/m/posts')->assertStatus(200);
        $this->getJson('/api/m/posts/does-not-exist')->assertStatus(404);
    }

    public function test_api_can_be_disabled(): void
    {
        Bp_options::updateOrCreate(['option_name' => 'api_enabled'], ['option_value' => 'no', 'autoload' => 'yes']);
        $this->getJson('/api/m/home')->assertStatus(503);
    }

    /**
     * Every shipped theme must render the full front-end without a 404 or error,
     * so activating any of them is safe. Guards against a theme shipping with a
     * missing view (the bug that broke /events, /faq and /contact before).
     */
    #[DataProvider('themes')]
    public function test_theme_renders_every_front_route(string $slug): void
    {
        Bp_options::updateOrCreate(['option_name' => 'theme'], ['option_value' => $slug, 'autoload' => 'yes']);

        $post = Bp_post::where('post_type', 'post')->where('translate_id', 0)->firstOrFail();
        $category = Bp_tax::where('tax_type', 'cat')->first();

        $this->get('/')->assertStatus(200)->assertSee('Beyond Plus CMS');
        $this->get('/blog')->assertStatus(200);
        $this->get('/search?q=Multilingual')->assertStatus(200)->assertSee('Building Multilingual Content');
        $this->get('/events')->assertStatus(200);
        $this->get('/events?month=2026-08')->assertStatus(200);
        $this->get('/faq')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);

        // A single post (uses single.blade + the shared .bp-content wrapper).
        $this->get('/'.$post->post_link)->assertStatus(200)->assertSee($post->title, false);

        // A category / term listing.
        if ($category) {
            $this->get('/cat/'.$category->tax_link)->assertStatus(200);
        }
    }

    public static function themes(): array
    {
        return [
            'default (Aurora)'    => ['default'],
            'bptheme1 (Meridian)' => ['bptheme1'],
            'bptheme2 (Nocturne)' => ['bptheme2'],
            'bptheme3 (Terra)'    => ['bptheme3'],
            'bptheme4 (Pulse)'    => ['bptheme4'],
        ];
    }
}
