<?php

namespace App\Repositories;

use App\Models\Customers;
use App\Services\OtpNotifier;
use DB;
use Hash;

class CustomersRepo
{
    protected $otpNotifier;

    public function __construct(OtpNotifier $otpNotifier)
    {
        $this->otpNotifier = $otpNotifier;
    }

    public function createCustomer($request)
    {
        return DB::transaction(function () use ($request) {

            $customers = new Customers;

            $input = $request->all();

            $customers->first_name = $input['firstname'];
            $customers->last_name = $input['lastname'] ?? '';
            $customers->phone = $input['phone'] ?? null;
            $customers->email = $input['email'] ?? null;

            $customers->customer_types_id = 1;
            $customers->is_verified = 0;
            $customers->status = 1;
            $customers->total_reward_points = 0; // for new customer reward points
            $customers->otpcode = mt_rand(100000, 999999);
            $customers->activation_code = sha1(mt_rand(10000, 99999).time().($input['phone'] ?? $input['email'] ?? ''));
            $customers->password = Hash::make($input['password']);

            $saved = $customers->save();

            if ($saved) {
                // Deliver the OTP over the configured channel (SMS/email), or log it
                // when no gateway is enabled (see admin Configuration page).
                $this->otpNotifier->send($customers, $customers->otpcode);
            }

            return ($saved) ? $customers : false;
        });
    }
}
