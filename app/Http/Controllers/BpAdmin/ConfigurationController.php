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
        'otp_channel'          => 'auto',  // auto | sms | email
        'api_enabled'          => 'yes',   // yes | no
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
        foreach ($this->defaults as $key => $default) {
            Bp_options::updateOrCreate(
                ['option_name' => $key],
                ['option_value' => $request->input($key, $default), 'autoload' => 'yes']
            );
        }

        return redirect()->back()->with('success', 'Configuration saved.');
    }
}
