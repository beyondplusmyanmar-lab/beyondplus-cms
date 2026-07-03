<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bp_options;

class OptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bp_options::truncate();
        $option_name  = array('siteurl', 'home', 'blogname', 'blogdescription', 'theme', 'admin_email', 'version',
            'registration_type', 'api_enabled', 'sms_enabled', 'sms_provider', 'sms_sender', 'sms_api_token',
            'mail_enabled', 'mail_provider', 'mailgun_domain', 'mailgun_secret', 'mail_from', 'spa_url', 'cors_origins', 'frontend_mode', 'otp_channel', 'active_plugins');
        $option_value = array('http://localhost', 'http://localhost', 'Beyond Plus CMS', 'A Beyond Plus CMS sample site', 'default', 'admin@example.com', '2.2.0',
            'phone', 'yes', 'no', 'smspoh', '', '',
            'no', 'mailgun', '', '', '', '', '', 'theme', 'auto', '["smspoh","mailgun"]');
        for ($i = 0; $i < count($option_name); $i++) {
            Bp_options::insert([
                'option_name'  => $option_name[$i],
                'option_value' => $option_value[$i],
                'autoload'     => 'yes',
                'created_at'   => now(),
            ]);
        }
    }
}
