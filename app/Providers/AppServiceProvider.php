<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
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
        //
    }
}
