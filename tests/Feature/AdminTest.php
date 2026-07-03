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
}
