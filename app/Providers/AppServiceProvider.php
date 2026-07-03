<?php

namespace App\Providers;

use App\Mail\Transport\ConfigMailgunTransport;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Legacy lowercase model aliases still referenced throughout the CMS.
     *
     * @var array<string, class-string>
     */
    protected array $modelAliases = [
        'bp_post' => \App\Models\Bp_post::class,
        'bp_tax' => \App\Models\Bp_tax::class,
        'bp_menu' => \App\Models\Bp_menu::class,
        'bp_relationship' => \App\Models\Bp_relationship::class,
        'bp_slider' => \App\Models\Bp_slider::class,
        'bp_module' => \App\Models\Bp_module::class,
        'bp_custom' => \App\Models\Bp_custom::class,
        'bp_messages' => \App\Models\Bp_messages::class,
        'bp_options' => \App\Models\Bp_options::class,
        'bp_block' => \App\Models\Bp_block::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();

        foreach ($this->modelAliases as $alias => $class) {
            $loader->alias($alias, $class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load active plugins so they can register their action/filter hooks.
        \App\Support\Plugin::boot();

        // Custom transport that delivers via the Mailgun credentials in bp_options.
        Mail::extend('bp_mailgun', fn () => new ConfigMailgunTransport());

        // When mail is enabled in the admin Configuration, route all app mail
        // through it. Wrapped in try/catch so a not-yet-migrated DB is harmless.
        try {
            if (bp_option('mail_enabled', 'no') === 'yes') {
                config([
                    'mail.default'      => 'bp_mailgun',
                    'mail.from.address' => bp_option('mail_from') ?: config('mail.from.address'),
                    'mail.from.name'    => optional(site_information('blogname'))->option_value ?: config('app.name'),
                ]);
            }
        } catch (\Throwable $e) {
            // DB unavailable (e.g. before migrations) — keep the default mailer.
        }

        // Restrict API CORS to the origins configured on the admin Configuration
        // page (blank = allow all, the framework default).
        try {
            $origins = trim((string) bp_option('cors_origins', ''));
            if ($origins !== '') {
                $list = array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $origins))));
                if ($list) {
                    config(['cors.allowed_origins' => $list]);
                }
            }
        } catch (\Throwable $e) {
            // DB unavailable — keep the default CORS policy.
        }
    }
}
