<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Config-driven transactional mail sender via the Mailgun HTTP API.
 * Credentials come from bp_options (admin Configuration page).
 */
class MailService
{
    public function enabled(): bool
    {
        return bp_option('mail_enabled', 'no') === 'yes'
            && bp_option('mail_provider', 'mailgun') === 'mailgun'
            && bp_option('mailgun_domain') !== ''
            && bp_option('mailgun_secret') !== '';
    }

    public function send(string $to, string $subject, string $body): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        try {
            $domain = bp_option('mailgun_domain');

            $response = Http::asForm()
                ->withBasicAuth('api', bp_option('mailgun_secret'))
                ->timeout(10)
                ->post("https://api.mailgun.net/v3/{$domain}/messages", [
                    'from'    => bp_option('mail_from') ?: "no-reply@{$domain}",
                    'to'      => $to,
                    'subject' => $subject,
                    'text'    => $body,
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Mail send failed: '.$e->getMessage());
            return false;
        }
    }
}
