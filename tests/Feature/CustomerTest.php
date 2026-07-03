<?php

namespace Tests\Feature;

use App\Models\Bp_options;
use App\Models\Customers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_can_register_then_activate(): void
    {
        $phone = '09112223344';

        $this->post('/customer/sign-up', [
            'firstname'             => 'Test',
            'phone'                 => $phone,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $customer = Customers::where('phone', $phone)->first();
        $this->assertNotNull($customer);
        $this->assertEquals(0, (int) $customer->is_verified);

        // OTP delivery falls back to the log (no provider configured); the code
        // is on the record, so activate with it (session carries verify_phone).
        $this->post('/customer/activate', ['activation_code' => $customer->otpcode]);

        $customer->refresh();
        $this->assertEquals(1, (int) $customer->is_verified);
    }

    public function test_registration_can_be_closed(): void
    {
        Bp_options::updateOrCreate(['option_name' => 'registration_enabled'], ['option_value' => 'no', 'autoload' => 'yes']);

        $this->get('/customer/sign-up')->assertRedirect();
        $this->post('/customer/sign-up', [
            'firstname'             => 'X',
            'phone'                 => '09998887766',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertNull(Customers::where('phone', '09998887766')->first());
    }
}
