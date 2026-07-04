<?php

namespace Tests\Feature;

use App\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        \Illuminate\Support\Facades\Cache::flush(); // reset the login rate limiter
    }

    private function admin(): Admin
    {
        return Admin::where('email', 'admin@example.com')->firstOrFail();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $this->actingAs($this->admin(), 'admins')
            ->get('/bp-admin')
            ->assertStatus(200)
            ->assertSee('Dashboard');
    }

    public function test_admin_pages_load(): void
    {
        foreach (['post', 'page', 'media', 'slider', 'menu', 'configuration', 'themes', 'plugins'] as $page) {
            $this->actingAs($this->admin(), 'admins')
                ->get("/bp-admin/{$page}")
                ->assertStatus(200);
        }
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/bp-admin/post')->assertRedirect();
    }

    public function test_default_login_authenticates(): void
    {
        $this->post('/bp-admin/login', ['email' => 'admin@example.com', 'password' => 'password']);
        $this->assertTrue(auth()->guard('admins')->check());
    }

    public function test_hardened_login_makes_default_path_a_decoy(): void
    {
        \App\Models\Bp_options::updateOrCreate(
            ['option_name' => 'admin_login_path'],
            ['option_value' => 'secretdoor', 'autoload' => 'yes']
        );

        // Even correct credentials at the default (now decoy) path must not log in.
        $this->post('/bp-admin/login', ['email' => 'admin@example.com', 'password' => 'password']);
        $this->assertFalse(auth()->guard('admins')->check());
    }

    public function test_system_page_loads(): void
    {
        // Disable the update check so the page renders without a network call.
        \App\Models\Bp_options::updateOrCreate(['option_name' => 'update_check'], ['option_value' => 'no', 'autoload' => 'yes']);
        $this->actingAs($this->admin(), 'admins')->get('/bp-admin/configuration/system')
            ->assertStatus(200)->assertSee('2.3.0');
    }

    public function test_system_flow_page_loads(): void
    {
        $this->actingAs($this->admin(), 'admins')->get('/bp-admin/configuration/flow')
            ->assertStatus(200)->assertSee('System flow');
    }

    public function test_activity_log_page_loads(): void
    {
        $this->actingAs($this->admin(), 'admins')->get('/bp-admin/activity')->assertStatus(200);
    }

    public function test_login_is_recorded_as_activity(): void
    {
        $this->post('/bp-admin/login', ['email' => 'admin@example.com', 'password' => 'password']);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'auth', 'description' => 'signed in']);
    }

    public function test_activity_log_exports_csv(): void
    {
        $res = $this->actingAs($this->admin(), 'admins')->get('/bp-admin/activity/export');
        $res->assertStatus(200)->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_failed_login_is_recorded_as_activity(): void
    {
        $this->post('/bp-admin/login', ['email' => 'x@example.com', 'password' => 'wrong-password']);
        $this->assertTrue(
            \Spatie\Activitylog\Models\Activity::where('description', 'like', 'failed sign-in%')->exists()
        );
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/bp-admin/login', ['email' => 'admin@example.com', 'password' => 'wrong-password']);
        }

        // After 5 failures the next attempt is locked out — even correct creds.
        $response = $this->post('/bp-admin/login', ['email' => 'admin@example.com', 'password' => 'password']);
        $this->assertFalse(auth()->guard('admins')->check());
        $response->assertSee('Too many', false);
    }
}
