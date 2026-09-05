<?php

namespace Tests\Feature;

use App\Models\Bp_comment;
use App\Models\Bp_post;
use App\Models\Customers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function customer(): Customers
    {
        return Customers::where('email', 'customer@example.com')->firstOrFail();
    }

    private function seededPost(): Bp_post
    {
        return Bp_post::where('post_type', 'post')->firstOrFail();
    }

    public function test_guest_is_redirected_to_sign_in_and_writes_nothing(): void
    {
        $before = Bp_comment::count();

        $this->post('/comment', ['post_id' => $this->seededPost()->id, 'body' => 'from a guest'])
            ->assertStatus(302)
            ->assertRedirect('/customer/sign-in');

        $this->assertDatabaseMissing('bp_comment', ['body' => 'from a guest']);
        $this->assertSame($before, Bp_comment::count(), 'a guest must not create a comment');
    }

    public function test_guest_ajax_request_is_rejected_with_401(): void
    {
        $before = Bp_comment::count();

        $this->postJson('/comment', ['post_id' => $this->seededPost()->id, 'body' => 'ajax guest'])
            ->assertStatus(401);

        $this->assertDatabaseMissing('bp_comment', ['body' => 'ajax guest']);
        $this->assertSame($before, Bp_comment::count());
    }

    public function test_authenticated_customer_can_comment(): void
    {
        $customer = $this->customer();
        $post = $this->seededPost();

        $this->actingAs($customer)
            ->post('/comment', ['post_id' => $post->id, 'body' => 'a real comment'])
            ->assertStatus(200)
            ->assertSee('1', false);

        $this->assertDatabaseHas('bp_comment', [
            'post_id' => $post->id,
            'user_id' => $customer->id,
            'body' => 'a real comment',
            'active' => 'no', // column default: not client-controlled
        ]);
    }

    public function test_client_cannot_set_id_active_or_author(): void
    {
        $customer = $this->customer();
        $post = $this->seededPost();

        $this->actingAs($customer)->post('/comment', [
            'post_id' => $post->id,
            'body' => 'mass assignment attempt',
            'active' => 'yes',
            'id' => 999,
            'user_id' => 4242,
        ])->assertStatus(200);

        $comment = Bp_comment::where('body', 'mass assignment attempt')->firstOrFail();

        $this->assertSame('no', $comment->active, 'active must come from the column default');
        $this->assertEquals($customer->id, $comment->user_id, 'author must come from the credential');
        $this->assertNotEquals(999, $comment->id, 'client must not choose the primary key');
    }

    public function test_author_relation_resolves_to_a_customer(): void
    {
        // user_id holds a customers.id, because the "web" guard resolves
        // through the customers provider.
        $this->assertInstanceOf(Customers::class, (new Bp_comment)->author()->getRelated());
    }

    public function test_comment_and_its_author_render_on_the_post_page(): void
    {
        $customer = $this->customer();
        $post = $this->seededPost();

        Bp_comment::create([
            'post_id' => $post->id,
            'user_id' => $customer->id,
            'body' => 'a visible comment body',
        ]);

        // The comment block sits inside @auth, so view it as a signed-in customer.
        $this->actingAs($customer)
            ->get('/'.$post->post_link)
            ->assertStatus(200)
            ->assertSee('a visible comment body')
            ->assertSee('Demo Customer');
    }

    public function test_body_and_post_id_are_validated(): void
    {
        $customer = $this->customer();
        $before = Bp_comment::count();

        // missing body
        $this->actingAs($customer)
            ->postJson('/comment', ['post_id' => $this->seededPost()->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('body');

        // post that does not exist
        $this->actingAs($customer)
            ->postJson('/comment', ['post_id' => 999999, 'body' => 'orphan'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('post_id');

        $this->assertSame($before, Bp_comment::count(), 'no comment is written on a validation failure');
    }
}
