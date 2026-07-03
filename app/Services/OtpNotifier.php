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
        $channel = bp_option('otp_channel', 'auto');   // auto | sms | email
        $sent = false;

        $trySms = fn () => (! empty($customer->phone) && $this->sms->enabled())
            ? $this->sms->send($customer->phone, $message) : false;

        $tryMail = fn () => (! empty($customer->email) && $this->mail->enabled())
            ? $this->mail->send($customer->email, 'Your verification code', $message) : false;

        // The admin picks which provider delivers the OTP; "auto" prefers SMS
        // then falls back to email (the original behaviour).
        if ($channel === 'sms') {
            $sent = $trySms();
        } elseif ($channel === 'email') {
            $sent = $tryMail();
        } else {
            $sent = $trySms() ?: $tryMail();
        }

        if (! $sent) {
            // Chosen gateway not enabled/available — log so the flow still works locally.
            $target = $customer->phone ?: $customer->email;
            Log::info("OTP for {$target}: {$code}");
        }
    }
}
