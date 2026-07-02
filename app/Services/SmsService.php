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
    /** Credentials present (regardless of the on/off toggle). */
    public function configured(): bool
    {
        return bp_option('sms_api_token') !== '';
    }

    /** Turned on AND configured — used by the live OTP flow. */
    public function enabled(): bool
    {
        return bp_option('sms_enabled', 'no') === 'yes' && $this->configured();
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
            return $this->dispatch($to, $message);
        } catch (\Throwable $e) {
            Log::warning('SMS send failed: '.$e->getMessage());
            return false;
        }
    }

    /**
     * Send a test message using the saved credentials (ignores the on/off
     * toggle) and report the outcome for the admin Configuration page.
     *
     * @return array{ok: bool, message: string}
     */
    public function test(string $to): array
    {
        if (! $this->configured()) {
            return ['ok' => false, 'message' => 'No SMS API token configured.'];
        }

        try {
            $ok = $this->dispatch($to, 'Test message from '.config('app.name').'.');
            return ['ok' => $ok, 'message' => $ok ? 'SMS accepted by the gateway.' : 'The gateway rejected the request.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    protected function dispatch(string $to, string $message): bool
    {
        switch (bp_option('sms_provider', 'smspoh')) {
            case 'smspoh':
                $response = Http::withToken(bp_option('sms_api_token'))
                    ->acceptJson()
                    ->timeout(10)
                    ->post('https://api.smspoh.com/v1/messages/send', [
                        'to'      => $to,
                        'from'    => bp_option('sms_sender') ?: 'CMS',
                        'content' => $message,
                    ]);

                return $response->successful();
            default:
                return false;
        }
    }
}
