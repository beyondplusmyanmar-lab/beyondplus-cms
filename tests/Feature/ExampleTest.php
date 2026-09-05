<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The application boots and the admin login page renders.
     *
     * @return void
     */
    public function test_admin_login_page_loads()
    {
        $response = $this->get('/bp-admin/login');

        $response->assertStatus(200);
    }
}
