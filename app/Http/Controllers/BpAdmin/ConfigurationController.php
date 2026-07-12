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

        // Localises the human-readable labels; brand names, routes and code
        // identifiers (SMSPoh, /api/m/*, bp_store_image…) stay as-is.
        $t = fn (string $en, string $mm) => ($mm !== '' && app()->getLocale() === 'mm') ? $mm : $en;

        $flows = [
            [
                'title'   => $t('Customer OTP & notifications', 'ဖောက်သည် OTP နှင့် အကြောင်းကြားချက်များ'),
                'trigger' => ['label' => $t('Sign-up · Verify · Reset', 'အကောင့်ဖွင့် · အတည်ပြု · ပြန်သတ်မှတ်'), 'icon' => 'fa-user-plus'],
                'core'    => ['label' => $t('OTP dispatcher', 'OTP ပို့ဆောင်ချက်'), 'sub' => $t('channel: ', 'ချန်နယ်: ').bp_option('otp_channel', 'auto')],
                'providers' => [
                    ['label' => 'SMSPoh', 'sub' => 'SMS', 'slug' => 'smspoh', 'active' => $on('smspoh'), 'icon' => 'fa-comment', 'link' => url('bp-admin/plugins/settings?slug=smspoh')],
                    ['label' => 'Mailgun', 'sub' => $t('Email', 'အီးမေးလ်'), 'slug' => 'mailgun', 'active' => $on('mailgun'), 'icon' => 'fa-envelope', 'link' => url('bp-admin/plugins/settings?slug=mailgun')],
                    ['label' => $t('Log file', 'Log ဖိုင်'), 'sub' => $t('fallback when no provider', 'provider မရှိလျှင် အရန်'), 'active' => true, 'fallback' => true, 'icon' => 'fa-file-text-o'],
                ],
            ],
            [
                'title'   => $t('Image storage', 'ပုံ သိမ်းဆည်းမှု'),
                'trigger' => ['label' => $t('Media upload', 'မီဒီယာ တင်ခြင်း'), 'icon' => 'fa-image'],
                'core'    => ['label' => 'bp_store_image', 'sub' => $r2 ? $t('object storage', 'object storage') : $t('local disk', 'local disk')],
                'providers' => [
                    ['label' => 'Cloudflare R2', 'sub' => $t('object storage', 'object storage'), 'slug' => 'cloudflare-r2', 'active' => $r2, 'icon' => 'fa-cloud', 'link' => url('bp-admin/plugins/settings?slug=cloudflare-r2')],
                    ['label' => $t('Local disk', 'Local disk'), 'sub' => 'public/uploads', 'active' => ! $r2, 'fallback' => true, 'icon' => 'fa-hdd-o'],
                ],
            ],
            [
                'title'   => $t('Mobile app / SPA', 'Mobile app / SPA'),
                'trigger' => ['label' => $t('API request', 'API တောင်းဆိုမှု'), 'icon' => 'fa-mobile'],
                'core'    => ['label' => 'JSON API', 'sub' => $api ? $t('enabled', 'ဖွင့်ထား') : $t('disabled', 'ပိတ်ထား')],
                'providers' => [
                    ['label' => '/api/m/*', 'sub' => $api ? $t('serving content', 'အကြောင်းအရာ ပေးနေသည်') : $t('returns 503', '503 ပြန်ပေးသည်'), 'active' => $api, 'icon' => 'fa-plug', 'link' => url('bp-admin/configuration')],
                ],
            ],
            [
                'title'   => $t('Feedback notifications', 'Feedback အကြောင်းကြားချက်များ'),
                'trigger' => ['label' => $t('Contact form submitted', 'Contact ဖောင် တင်သွင်းသည်'), 'icon' => 'fa-comment'],
                'core'    => ['label' => 'feedback_received', 'sub' => $t('CMS action hook', 'CMS action hook')],
                'providers' => [
                    ['label' => 'Telegram Feedback', 'sub' => 'Telegram Bot API', 'slug' => 'telegram-feedback', 'active' => $on('telegram-feedback'), 'icon' => 'fa-paper-plane', 'link' => url('bp-admin/plugins/settings?slug=telegram-feedback')],
                    ['label' => $t('Feedback inbox', 'Feedback inbox'), 'sub' => $t('always stored', 'အမြဲ သိမ်းသည်'), 'active' => true, 'fallback' => true, 'icon' => 'fa-inbox', 'link' => url('bp-admin/feedback')],
                ],
            ],
            [
                'title'   => $t('Front-end features', 'ရှေ့ဆုံး လုပ်ဆောင်ချက်များ'),
                'trigger' => ['label' => $t('Site visitor', 'ဆိုက် ဧည့်သည်'), 'icon' => 'fa-globe'],
                'core'    => ['label' => $t('Theme: ', 'Theme: ').bp_option('theme', 'default'), 'sub' => $t('active theme', 'အသုံးပြုဆဲ theme')],
                'providers' => [
                    ['label' => $t('FAQ page', 'FAQ စာမျက်နှာ'), 'sub' => '/faq', 'active' => bp_option('faq_enabled', 'yes') === 'yes', 'fallback' => true, 'icon' => 'fa-question-circle', 'link' => url('bp-admin/faq')],
                    ['label' => $t('Contact form', 'Contact ဖောင်'), 'sub' => '/contact', 'active' => bp_option('feedback_enabled', 'yes') === 'yes', 'fallback' => true, 'icon' => 'fa-envelope-o', 'link' => url('bp-admin/feedback')],
                    ['label' => $t('Events calendar', 'ပွဲ ပြက္ခဒိန်'), 'sub' => '/events', 'active' => true, 'fallback' => true, 'icon' => 'fa-calendar', 'link' => url('bp-admin/news')],
                ],
            ],
            [
                'title'   => 'Commerce',
                'trigger' => ['label' => $t('Product / shop page', 'ကုန်ပစ္စည်း / shop စာမျက်နှာ'), 'icon' => 'fa-shopping-cart'],
                'core'    => ['label' => $t('Business theme hooks', 'Business theme hook များ'), 'sub' => 'featured products · promotions · locations'],
                'providers' => [
                    ['label' => 'Commerce', 'sub' => $t('catalogue · /shop', 'ကုန်ပစ္စည်း · /shop'), 'slug' => 'commerce', 'active' => $on('commerce'), 'icon' => 'fa-shopping-cart', 'link' => $on('commerce') ? url('bp-admin/commerce') : url('bp-admin/plugins/view?slug=commerce')],
                    ['label' => 'Commerce Checkout', 'sub' => $t('cart · orders (COD)', 'cart · အော်ဒါ (COD)'), 'slug' => 'commerce-checkout', 'active' => $on('commerce-checkout'), 'icon' => 'fa-shopping-bag', 'link' => $on('commerce-checkout') ? url('bp-admin/orders') : url('bp-admin/plugins/view?slug=commerce-checkout')],
                ],
            ],
        ];

        // Catch-all: any active plugin not covered by a curated flow above still
        // appears here, so no active add-on is invisible on this page.
        $covered = [];
        foreach ($flows as $f) {
            foreach ($f['providers'] as $p) {
                if (! empty($p['slug'])) {
                    $covered[] = $p['slug'];
                }
            }
        }
        $others = array_values(array_diff($active, $covered));
        if ($others) {
            $flows[] = [
                'title'   => $t('Other active plugins', 'အခြား အသုံးပြုဆဲ ပလပ်အင်များ'),
                'trigger' => ['label' => $t('Active add-ons', 'အသုံးပြုဆဲ add-on များ'), 'icon' => 'fa-plug'],
                'core'    => ['label' => $t('Hook system', 'Hook စနစ်'), 'sub' => count($others).' '.$t('not shown above', 'ခု အထက်တွင် မပြထား')],
                'providers' => array_map(function ($slug) {
                    $meta = \App\Support\Plugin::meta($slug);
                    return [
                        'label'  => $meta['name'] ?? $slug,
                        'sub'    => $meta['category'] ?? 'plugin',
                        'slug'   => $slug,
                        'active' => true,
                        'icon'   => 'fa-plug',
                        'link'   => url('bp-admin/plugins/view?slug='.$slug),
                    ];
                }, $others),
            ];
        }

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
