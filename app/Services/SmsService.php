<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Config-driven SMS sender. Provider + credentials come from bp_options
 * (managed on the admin Configuration page). Currently supports SMSPoh.
 */
class SmsService
{
    public function enabled(): bool
    {
        return bp_option('sms_enabled', 'no') === 'yes' && bp_option('sms_api_token') !== '';
    }

    /**
     * @return bool  true if the message was accepted by the gateway
     */
    public function send(string $to, string $message): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        try {
            switch (bp_option('sms_provider', 'smspoh')) {
                case 'smspoh':
                    return $this->sendViaSmsPoh($to, $message);
                default:
                    return false;
            }
        } catch (\Throwable $e) {
            Log::warning('SMS send failed: '.$e->getMessage());
            return false;
        }
    }

    protected function sendViaSmsPoh(string $to, string $message): bool
    {
        $response = Http::withToken(bp_option('sms_api_token'))
            ->acceptJson()
            ->timeout(10)
            ->post('https://api.smspoh.com/v1/messages/send', [
                'to'      => $to,
                'from'    => bp_option('sms_sender') ?: 'CMS',
                'content' => $message,
            ]);

        return $response->successful();
    }
}
