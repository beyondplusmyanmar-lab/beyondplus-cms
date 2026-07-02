<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bp_options;
use App\Services\SmsService;
use App\Services\MailService;

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

    public function testSms(Request $request, SmsService $sms)
    {
        $to = trim((string) $request->input('to'));
        if ($to === '') {
            return response()->json(['ok' => false, 'message' => 'Enter a phone number to test.']);
        }

        return response()->json($sms->test($to));
    }

    public function testEmail(Request $request, MailService $mail)
    {
        $to = trim((string) $request->input('to'));
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['ok' => false, 'message' => 'Enter a valid email address to test.']);
        }

        return response()->json($mail->test($to));
    }
}
