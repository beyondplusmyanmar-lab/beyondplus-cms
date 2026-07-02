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
    /** Credentials present (regardless of the on/off toggle). */
    public function configured(): bool
    {
        return bp_option('mail_provider', 'mailgun') === 'mailgun'
            && bp_option('mailgun_domain') !== ''
            && bp_option('mailgun_secret') !== '';
    }

    /** Turned on AND configured — used by the live flows. */
    public function enabled(): bool
    {
        return bp_option('mail_enabled', 'no') === 'yes' && $this->configured();
    }

    public function send(string $to, string $subject, string $body): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        try {
            return $this->dispatch($to, $subject, $body);
        } catch (\Throwable $e) {
            Log::warning('Mail send failed: '.$e->getMessage());
            return false;
        }
    }

    /**
     * Send a test email using the saved credentials (ignores the on/off toggle).
     *
     * @return array{ok: bool, message: string}
     */
    public function test(string $to): array
    {
        if (! $this->configured()) {
            return ['ok' => false, 'message' => 'Mailgun domain and secret are required.'];
        }

        try {
            $ok = $this->dispatch($to, 'Test email from '.config('app.name'), 'This is a test email from your CMS configuration.');
            return ['ok' => $ok, 'message' => $ok ? 'Email accepted by Mailgun.' : 'Mailgun rejected the request.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    protected function dispatch(string $to, string $subject, string $body): bool
    {
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
    }
}
