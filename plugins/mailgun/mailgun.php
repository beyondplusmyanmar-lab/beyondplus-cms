<?php

/**
 * Mailgun provider plugin.
 *
 * Registers the `send_mail` filter so the CMS can deliver email through the
 * Mailgun HTTP API. The filter receives (bool $sent, string $to, string
 * $subject, string $body) and returns true when accepted. Credentials come from
 * the Configuration page (mailgun_domain, mailgun_secret, mail_from). Loaded
 * only when this plugin is active.
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

bp_add_filter('send_mail', function ($sent, $to, $subject, $body) {
    if ($sent) {
        return $sent;
    }
    $domain = bp_option('mailgun_domain', '');
    $secret = bp_option('mailgun_secret', '');
    if ($domain === '' || $secret === '') {
        return false;
    }

    try {
        $response = Http::asForm()
            ->withBasicAuth('api', $secret)
            ->timeout(10)
            ->post("https://api.mailgun.net/v3/{$domain}/messages", [
                'from'    => bp_option('mail_from') ?: "no-reply@{$domain}",
                'to'      => $to,
                'subject' => $subject,
                'text'    => $body,
            ]);

        return $response->successful();
    } catch (\Throwable $e) {
        Log::warning('Mailgun send failed: '.$e->getMessage());
        return false;
    }
});

// Raw-MIME sending for general Laravel mail (Mailables), used by the app mail
// transport so ALL email — not just OTP — flows through this plugin.
bp_add_filter('send_mail_mime', function ($sent, array $recipients, string $mime) {
    if ($sent) {
        return $sent;
    }
    $domain = bp_option('mailgun_domain', '');
    $secret = bp_option('mailgun_secret', '');
    if ($domain === '' || $secret === '') {
        return false;
    }

    try {
        $response = Http::withBasicAuth('api', $secret)
            ->timeout(15)
            ->attach('message', $mime, 'message.mime')
            ->post("https://api.mailgun.net/v3/{$domain}/messages.mime", [
                'to' => implode(',', $recipients),
            ]);

        return $response->successful();
    } catch (\Throwable $e) {
        Log::warning('Mailgun MIME send failed: '.$e->getMessage());
        return false;
    }
});
