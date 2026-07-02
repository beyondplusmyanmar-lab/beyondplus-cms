<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

/**
 * A Laravel/Symfony mail transport that delivers via the Mailgun HTTP API
 * using the domain/secret stored in bp_options (admin Configuration page),
 * so all application mail follows the runtime configuration without needing
 * the symfony/mailgun-mailer package.
 */
class ConfigMailgunTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $domain = bp_option('mailgun_domain');
        $secret = bp_option('mailgun_secret');

        if ($domain === '' || $secret === '') {
            throw new \RuntimeException('Mailgun is not configured (domain/secret missing).');
        }

        $recipients = array_map(
            fn ($address) => $address->toString(),
            $message->getEnvelope()->getRecipients()
        );

        $response = Http::withBasicAuth('api', $secret)
            ->timeout(15)
            ->attach('message', $message->toString(), 'message.mime')
            ->post("https://api.mailgun.net/v3/{$domain}/messages.mime", [
                'to' => implode(',', $recipients),
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Mailgun rejected the message: HTTP '.$response->status());
        }
    }

    public function __toString(): string
    {
        return 'bp-mailgun';
    }
}
