<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bp_options;

class ConfigurationController extends Controller
{
    /**
     * Site-wide configuration options and their defaults. Provider credentials
     * (SMS / email) live on their own plugin Settings pages, not here.
     *
     * @var array<string, string>
     */
    protected $defaults = [
        'registration_enabled' => 'yes',   // yes | no — allow new customer sign-ups
        'registration_type'    => 'phone', // phone | email | both
        'faq_enabled'          => 'yes',   // yes | no — public /faq page
        'feedback_enabled'     => 'yes',   // yes | no — public /feedback form
        'otp_channel'          => 'auto',  // auto | sms | email
        'api_enabled'          => 'yes',   // yes | no
        'admin_login_path'     => '',      // secret admin login slug; blank = default bp-admin/login
        'developer_ips'        => '',      // IPs/CIDRs that may see the 500 developer log
        'update_check'         => 'yes',   // yes | no — check GitHub for core updates
        'update_repo'          => '',      // owner/repo to check; blank = default repo
        'spa_url'              => '',       // public URL of the headless/SPA app
        'cors_origins'         => '',       // allowed API origins; blank = allow all (*)
        'frontend_mode'        => 'theme',  // theme | spa | headless
    ];

    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {
        $config = [];
        foreach ($this->defaults as $key => $default) {
            $config[$key] = bp_option($key, $default);
        }

        return view('bp-admin.configuration.index', ['config' => $config]);
    }

    /** System info + core update status (checked against GitHub releases). */
    public function system(\Illuminate\Http\Request $request)
    {
        return view('bp-admin.configuration.system', [
            'update'  => \App\Support\CoreUpdate::check($request->boolean('check')),
            'repo'    => \App\Support\CoreUpdate::repo(),
            'php'     => PHP_VERSION,
            'laravel' => app()->version(),
        ]);
    }

    /** A visual "system flow" of how the CMS routes services through plugins. */
    public function flow()
    {
        $active = \App\Support\Plugin::active();
        $on = fn (string $slug) => in_array($slug, $active, true);
        $r2 = $on('cloudflare-r2');
        $api = bp_option('api_enabled', 'yes') === 'yes';

        $flows = [
            [
                'title'   => 'Customer OTP & notifications',
                'trigger' => ['label' => 'Sign-up · Verify · Reset', 'icon' => 'fa-user-plus'],
                'core'    => ['label' => 'OTP dispatcher', 'sub' => 'channel: '.bp_option('otp_channel', 'auto')],
                'providers' => [
                    ['label' => 'SMSPoh', 'sub' => 'SMS', 'slug' => 'smspoh', 'active' => $on('smspoh'), 'icon' => 'fa-comment', 'link' => url('bp-admin/plugins/settings?slug=smspoh')],
                    ['label' => 'Mailgun', 'sub' => 'Email', 'slug' => 'mailgun', 'active' => $on('mailgun'), 'icon' => 'fa-envelope', 'link' => url('bp-admin/plugins/settings?slug=mailgun')],
                    ['label' => 'Log file', 'sub' => 'fallback when no provider', 'active' => true, 'fallback' => true, 'icon' => 'fa-file-text-o'],
                ],
            ],
            [
                'title'   => 'Image storage',
                'trigger' => ['label' => 'Media upload', 'icon' => 'fa-image'],
                'core'    => ['label' => 'bp_store_image', 'sub' => $r2 ? 'object storage' : 'local disk'],
                'providers' => [
                    ['label' => 'Cloudflare R2', 'sub' => 'object storage', 'slug' => 'cloudflare-r2', 'active' => $r2, 'icon' => 'fa-cloud', 'link' => url('bp-admin/plugins/settings?slug=cloudflare-r2')],
                    ['label' => 'Local disk', 'sub' => 'public/uploads', 'active' => ! $r2, 'fallback' => true, 'icon' => 'fa-hdd-o'],
                ],
            ],
            [
                'title'   => 'Mobile app / SPA',
                'trigger' => ['label' => 'API request', 'icon' => 'fa-mobile'],
                'core'    => ['label' => 'JSON API', 'sub' => $api ? 'enabled' : 'disabled'],
                'providers' => [
                    ['label' => '/api/m/*', 'sub' => $api ? 'serving content' : 'returns 503', 'active' => $api, 'icon' => 'fa-plug', 'link' => url('bp-admin/configuration')],
                ],
            ],
            [
                'title'   => 'Feedback notifications',
                'trigger' => ['label' => 'Contact form submitted', 'icon' => 'fa-comment'],
                'core'    => ['label' => 'feedback_received', 'sub' => 'CMS action hook'],
                'providers' => [
                    ['label' => 'Telegram Feedback', 'sub' => 'Telegram Bot API', 'slug' => 'telegram-feedback', 'active' => $on('telegram-feedback'), 'icon' => 'fa-paper-plane', 'link' => url('bp-admin/plugins/settings?slug=telegram-feedback')],
                    ['label' => 'Feedback inbox', 'sub' => 'always stored', 'active' => true, 'fallback' => true, 'icon' => 'fa-inbox', 'link' => url('bp-admin/feedback')],
                ],
            ],
            [
                'title'   => 'Front-end features',
                'trigger' => ['label' => 'Site visitor', 'icon' => 'fa-globe'],
                'core'    => ['label' => 'Theme: '.bp_option('theme', 'default'), 'sub' => 'active theme'],
                'providers' => [
                    ['label' => 'FAQ page', 'sub' => '/faq', 'active' => bp_option('faq_enabled', 'yes') === 'yes', 'fallback' => true, 'icon' => 'fa-question-circle', 'link' => url('bp-admin/faq')],
                    ['label' => 'Contact form', 'sub' => '/contact', 'active' => bp_option('feedback_enabled', 'yes') === 'yes', 'fallback' => true, 'icon' => 'fa-envelope-o', 'link' => url('bp-admin/feedback')],
                    ['label' => 'Events calendar', 'sub' => '/events', 'active' => true, 'fallback' => true, 'icon' => 'fa-calendar', 'link' => url('bp-admin/news')],
                ],
            ],
        ];

        return view('bp-admin.configuration.flow', ['flows' => $flows, 'activeCount' => count($active)]);
    }

    public function update(Request $request)
    {
        // All-or-nothing: don't leave a half-saved config if one option fails.
        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            foreach ($this->defaults as $key => $default) {
                // Empty form fields arrive as null (ConvertEmptyStringsToNull
                // middleware); option_value is NOT NULL, so coalesce to the default
                // and store a string.
                $value = (string) ($request->input($key, $default) ?? $default);

                if ($key === 'admin_login_path') {
                    $value = $this->sanitizeLoginPath($value);
                }

                if ($key === 'developer_ips') {
                    $value = $this->sanitizeIpList($value);
                }

                Bp_options::updateOrCreate(
                    ['option_name' => $key],
                    ['option_value' => $value, 'autoload' => 'yes']
                );
            }
        });

        return redirect()->back()->with('success', 'Configuration saved.');
    }

    /** Keep the secret login slug safe and clear of paths that already exist. */
    private function sanitizeLoginPath($value): string
    {
        $slug = strtolower(preg_replace('/[^a-z0-9\-]/i', '', (string) $value));

        $reserved = [
            'login', 'logout', 'myprofile', 'lang', 'dashboard', 'post', 'page', 'news',
            'media', 'slider', 'menu', 'block', 'account', 'permission', 'general',
            'configuration', 'themes', 'plugins', 'user', 'reports', 'custom', 'addcustom',
            'user-guide',
        ];

        return in_array($slug, $reserved, true) ? '' : $slug;
    }

    /** Keep only valid IP addresses and IPv4 CIDR ranges, comma separated. */
    private function sanitizeIpList($value): string
    {
        $entries = preg_split('/[\s,]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $valid = array_filter($entries, function ($entry) {
            if (str_contains($entry, '/')) {
                [$subnet, $bits] = explode('/', $entry, 2);
                return filter_var($subnet, FILTER_VALIDATE_IP) !== false
                    && is_numeric($bits) && (int) $bits >= 0 && (int) $bits <= 128;
            }
            return filter_var($entry, FILTER_VALIDATE_IP) !== false;
        });

        return implode(', ', array_unique($valid));
    }
}
