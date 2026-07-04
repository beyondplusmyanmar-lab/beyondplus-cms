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
