<?php

namespace Tests\Feature;

use App\Admin;
use App\Support\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): Admin
    {
        return Admin::where('email', 'admin@example.com')->firstOrFail();
    }

    public function test_plugins_page_lists_plugins(): void
    {
        $this->actingAs($this->admin(), 'admins')
            ->get('/bp-admin/plugins')
            ->assertStatus(200)
            ->assertSee('SMSPoh')
            ->assertSee('Mailgun');
    }

    public function test_activate_and_deactivate_plugin(): void
    {
        $this->actingAs($this->admin(), 'admins')
            ->post('/bp-admin/plugins/activate', ['slug' => 'sample-banner']);
        $this->assertContains('sample-banner', Plugin::active());

        $this->actingAs($this->admin(), 'admins')
            ->post('/bp-admin/plugins/deactivate', ['slug' => 'sample-banner']);
        $this->assertNotContains('sample-banner', Plugin::active());
    }

    public function test_plugin_settings_are_saved(): void
    {
        $this->actingAs($this->admin(), 'admins')->post('/bp-admin/plugins/settings', [
            'slug'      => 'smspoh',
            'api_url'   => 'https://api.smspoh.com/v1/messages/send',
            'api_token' => 'SECRET_TOKEN',
            'sender'    => 'CMS',
        ]);

        $this->assertSame('SECRET_TOKEN', bp_plugin_option('smspoh', 'api_token'));
    }

    public function test_plugin_update_is_detected_and_applied(): void
    {
        Plugin::activate('logbook');                                   // records the manifest version
        $this->assertFalse(Plugin::all()['logbook']['update_available']);

        // Simulate a plugin that was installed at an older version.
        \App\Models\Bp_options::updateOrCreate(
            ['option_name' => 'plugin_versions'],
            ['option_value' => json_encode(['logbook' => '0.9.0']), 'autoload' => 'yes']
        );
        $this->assertTrue(Plugin::all()['logbook']['update_available']);

        Plugin::update('logbook');                                     // runs new migrations, re-records version
        $this->assertSame('1.0.0', Plugin::installedVersion('logbook'));
        $this->assertFalse(Plugin::all()['logbook']['update_available']);
    }

    public function test_security_scan_flags_dangerous_code(): void
    {
        $tmp = sys_get_temp_dir().'/scan'.uniqid();
        mkdir($tmp);
        file_put_contents($tmp.'/evil.php', "<?php system(\$_GET['c']);");
        file_put_contents($tmp.'/clean.php', "<?php echo 'hi';");

        $scan = \App\Support\PackageGuard::scan($tmp);
        $this->assertNotEmpty($scan['critical']);

        array_map('unlink', glob($tmp.'/*'));
        rmdir($tmp);
    }

    public function test_scan_blocks_file_deletion(): void
    {
        $tmp = sys_get_temp_dir().'/scandel'.uniqid();
        mkdir($tmp);
        file_put_contents($tmp.'/a.php', "<?php unlink(\$file);");
        file_put_contents($tmp.'/b.php', "<?php \\Illuminate\\Support\\Facades\\File::deleteDirectory(\$dir);");

        // Deleting files/dirs is a critical (blocking) finding, not just a warning.
        $scan = \App\Support\PackageGuard::scan($tmp);
        $this->assertNotEmpty($scan['critical']);

        array_map('unlink', glob($tmp.'/*'));
        rmdir($tmp);
    }
}
