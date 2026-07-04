<?php

namespace Tests\Feature;

use App\Admin;
use App\Models\Bp_options;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqFeedbackTest extends TestCase
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

    public function test_public_faq_page_loads_when_enabled(): void
    {
        // The heading is localized (mm by default); assert on a seeded FAQ instead.
        $this->get('/faq')->assertStatus(200)->assertSee('How do I log in to the admin panel?');
    }

    public function test_public_faq_page_404_when_disabled(): void
    {
        Bp_options::updateOrCreate(['option_name' => 'faq_enabled'], ['option_value' => 'no', 'autoload' => 'yes']);
        $this->get('/faq')->assertStatus(404);
    }

    public function test_contact_page_loads_with_form(): void
    {
        $this->get('/contact')->assertStatus(200)->assertSee('name="message"', false);
    }

    public function test_contact_submission_is_stored(): void
    {
        $this->post('/contact', [
            'name' => 'Ann', 'email' => 'a@example.com', 'subject' => 'Hi', 'message' => 'Nice site',
        ]);
        $this->assertDatabaseHas('feedback', ['name' => 'Ann', 'subject' => 'Hi', 'is_read' => 0]);
    }

    public function test_contact_honeypot_drops_bots(): void
    {
        $this->post('/contact', ['name' => 'Bot', 'message' => 'spam', 'website' => 'http://spam.example']);
        $this->assertDatabaseMissing('feedback', ['name' => 'Bot']);
    }

    public function test_contact_form_blocked_when_disabled(): void
    {
        Bp_options::updateOrCreate(['option_name' => 'feedback_enabled'], ['option_value' => 'no', 'autoload' => 'yes']);
        $this->post('/contact', ['name' => 'X', 'message' => 'hi'])->assertStatus(404);
    }

    public function test_old_feedback_url_redirects_to_contact(): void
    {
        $this->get('/feedback')->assertRedirect('/contact');
    }

    public function test_admin_can_manage_faqs_and_read_feedback(): void
    {
        $this->actingAs($this->admin(), 'admins')->get('/bp-admin/faq')->assertStatus(200);
        $this->actingAs($this->admin(), 'admins')->get('/bp-admin/feedback')->assertStatus(200);

        $this->actingAs($this->admin(), 'admins')->post('/bp-admin/faq/store', [
            'question' => 'Test question?', 'answer' => 'Test answer.', 'sort_order' => 5, 'is_active' => 1,
        ]);
        $this->assertDatabaseHas('faqs', ['question' => 'Test question?']);
    }
}
