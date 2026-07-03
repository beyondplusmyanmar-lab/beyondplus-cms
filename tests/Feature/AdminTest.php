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
}
