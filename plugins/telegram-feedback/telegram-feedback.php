<?php

/**
 * Telegram Feedback — a demo "social connect" plugin.
 *
 * Shows how the CMS can notify an external service through its hook system:
 * when a visitor submits the contact / feedback form, the CMS fires the
 * `feedback_received` action and this plugin forwards a summary to a Telegram
 * chat via the Bot API. Credentials come from this plugin's Settings page.
 *
 * Educational only — plug in a real bot token + chat ID to see it work.
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (! function_exists('bp_telegram_send')) {
    /** Send a message to the configured Telegram chat. Returns true on success. */
    function bp_telegram_send(string $text): bool
    {
        $token = bp_plugin_option('telegram-feedback', 'bot_token');
        $chat  = bp_plugin_option('telegram-feedback', 'chat_id');

        if (! $token || ! $chat) {
            return false;                       // not configured yet
        }

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $chat,
                    'text'       => $text,
                    'parse_mode' => 'HTML',
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Telegram send failed: '.$e->getMessage());
            return false;
        }
    }
}

// Notify Telegram whenever the contact / feedback form is submitted.
bp_add_action('feedback_received', function ($feedback) {
    $text = "📩 <b>New feedback</b>\n"
        . 'From: '.e($feedback->name ?? 'Anonymous').' ('.e($feedback->email ?: 'no email').")\n"
        . ($feedback->subject ? 'Subject: '.e($feedback->subject)."\n" : '')
        . "\n".e((string) ($feedback->message ?? ''));

    bp_telegram_send($text);
});

// Test hook (used by the plugin's "Send test" button).
bp_add_filter('send_telegram', function ($sent, $message) {
    if ($sent) {
        return $sent;
    }

    return bp_telegram_send('🔔 '.$message);
});
