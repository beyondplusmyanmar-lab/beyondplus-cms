<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Delivers a one-time code to a customer over the best available channel:
 * SMS (to phone) or email, based on the admin Configuration settings.
 * Falls back to writing the code to the log when no gateway is enabled
 * (handy for local/demo use — no SMS/mail account required).
 */
class OtpNotifier
{
    public function __construct(
        protected SmsService $sms,
        protected MailService $mail,
    ) {}

    /**
     * @param  object  $customer  must expose ->phone and/or ->email
     */
    public function send($customer, $code): void
    {
        $message = "Your verification code is {$code}.";
        $sent = false;

        if (! empty($customer->phone) && $this->sms->enabled()) {
            $sent = $this->sms->send($customer->phone, $message);
        }

        if (! $sent && ! empty($customer->email) && $this->mail->enabled()) {
            $sent = $this->mail->send($customer->email, 'Your verification code', $message);
        }

        if (! $sent) {
            // No gateway enabled/available — log so the flow still works locally.
            $target = $customer->phone ?: $customer->email;
            Log::info("OTP for {$target}: {$code}");
        }
    }
}
