<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bp_options;

class ConfigurationController extends Controller
{
    /**
     * Managed configuration options and their defaults.
     * Keys flagged in $secret are masked and only overwritten when a new value
     * is submitted (so leaving the field blank keeps the stored value).
     *
     * @var array<string, string>
     */
    protected $defaults = [
        'registration_type' => 'phone',   // phone | email | both
        'api_enabled'       => 'yes',      // yes | no
        'sms_enabled'       => 'no',
        'sms_provider'      => 'smspoh',
        'sms_sender'        => '',
        'sms_api_token'     => '',
        'mail_enabled'      => 'no',
        'mail_provider'     => 'mailgun',
        'mailgun_domain'    => '',
        'mailgun_secret'    => '',
        'mail_from'         => '',
    ];

    /** @var string[] */
    protected $secret = ['sms_api_token', 'mailgun_secret'];

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
        foreach ($this->defaults as $key => $default) {
            $value = $request->input($key);

            // Don't clobber a stored secret with a blank submission.
            if (in_array($key, $this->secret, true) && ($value === null || $value === '')) {
                continue;
            }

            Bp_options::updateOrCreate(
                ['option_name' => $key],
                ['option_value' => $value ?? $default, 'autoload' => 'yes']
            );
        }

        return redirect()->back()->with('flash_message', 'Configuration saved.');
    }
}
