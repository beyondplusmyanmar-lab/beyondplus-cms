<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

/**
 * A Laravel/Symfony mail transport that delivers every application email through
 * the active mail-provider plugin via the `send_mail_mime` hook. The plugin
 * (e.g. Mailgun) performs the actual send, so all mail — not only OTP — flows
 * through the plugin/configuration model. Registered as the "bp_mailgun" mailer.
 */
class ConfigMailgunTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $recipients = array_map(
            fn ($address) => $address->toString(),
            $message->getEnvelope()->getRecipients()
        );

        $sent = bp_apply_filters('send_mail_mime', false, $recipients, $message->toString());

        if (! $sent) {
            throw new \RuntimeException(
                'No active email provider handled the message (enable and configure a mail plugin).'
            );
        }
    }

    public function __toString(): string
    {
        return 'bp-mail';
    }
}
