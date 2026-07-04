<?php

namespace Tests\Feature;

use App\Models\Bp_options;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
