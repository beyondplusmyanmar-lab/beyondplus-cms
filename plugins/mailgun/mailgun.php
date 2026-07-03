<?php

/**
 * Mailgun provider plugin.
 *
 * Registers `send_mail` (subject/body) and `send_mail_mime` (raw MIME, used by
 * the app mail transport) so ALL email flows through Mailgun's HTTP API.
 * Configuration comes from this plugin's Settings, falling back to the legacy
 * Configuration → Email fields. Loaded only when the plugin is active.
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Resolve Mailgun config: plugin settings first, then the legacy options. */
$bp_mailgun_config = function () {
    $domain = bp_plugin_option('mailgun', 'domain') ?: bp_option('mailgun_domain', '');
    return [
        'domain' => $domain,
        'secret' => bp_plugin_option('mailgun', 'secret') ?: bp_option('mailgun_secret', ''),
        'base'   => rtrim(bp_plugin_option('mailgun', 'api_base') ?: 'https://api.mailgun.net/v3', '/'),
        'from'   => bp_plugin_option('mailgun', 'from_email') ?: (bp_option('mail_from') ?: "no-reply@{$domain}"),
    ];
};

bp_add_filter('send_mail', function ($sent, $to, $subject, $body) use ($bp_mailgun_config) {
    if ($sent) {
        return $sent;
    }
    $c = $bp_mailgun_config();
    if ($c['domain'] === '' || $c['secret'] === '') {
        return false;
    }

    try {
        $response = Http::asForm()
            ->withBasicAuth('api', $c['secret'])
            ->timeout(10)
            ->post("{$c['base']}/{$c['domain']}/messages", [
                'from'    => $c['from'],
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
bp_add_filter('send_mail_mime', function ($sent, array $recipients, string $mime) use ($bp_mailgun_config) {
    if ($sent) {
        return $sent;
    }
    $c = $bp_mailgun_config();
    if ($c['domain'] === '' || $c['secret'] === '') {
        return false;
    }

    try {
        $response = Http::withBasicAuth('api', $c['secret'])
            ->timeout(15)
            ->attach('message', $mime, 'message.mime')
            ->post("{$c['base']}/{$c['domain']}/messages.mime", [
                'to' => implode(',', $recipients),
            ]);

        return $response->successful();
    } catch (\Throwable $e) {
        Log::warning('Mailgun MIME send failed: '.$e->getMessage());
        return false;
    }
});
