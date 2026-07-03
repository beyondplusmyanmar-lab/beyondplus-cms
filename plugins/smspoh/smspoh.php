<?php

/**
 * SMSPoh provider plugin.
 *
 * Registers the `send_sms` filter so the CMS can deliver SMS through SMSPoh.
 * The filter receives (bool $sent, string $to, string $message) and returns
 * true when the message was accepted. Credentials come from the Configuration
 * page (sms_api_token, sms_sender). Loaded only when this plugin is active.
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

bp_add_filter('send_sms', function ($sent, $to, $message) {
    if ($sent) {
        return $sent;                       // already delivered by another provider
    }

    // Read from this plugin's own settings, falling back to the legacy config.
    $token  = bp_plugin_option('smspoh', 'api_token') ?: bp_option('sms_api_token', '');
    $url    = bp_plugin_option('smspoh', 'api_url') ?: 'https://api.smspoh.com/v1/messages/send';
    $sender = bp_plugin_option('smspoh', 'sender') ?: (bp_option('sms_sender') ?: 'CMS');

    if ($token === '') {
        return false;                       // not configured
    }

    try {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(10)
            ->post($url, [
                'to'      => $to,
                'from'    => $sender,
                'content' => $message,
            ]);

        return $response->successful();
    } catch (\Throwable $e) {
        Log::warning('SMSPoh send failed: '.$e->getMessage());
        return false;
    }
});
