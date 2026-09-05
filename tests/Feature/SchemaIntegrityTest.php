<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Every application model must have a table that the migrations actually build.
 *
 * Three tables (bp_apikeytable, currency, site_settings) were reachable only on
 * deployments that already had them: no migration and no line of
 * sample-data.sql created them, so a fresh install produced a database the code
 * could not use. Nothing failed at boot, and route:list was happy, because
 * Eloquent does not touch a table until something queries it.
 *
 * This guard runs against the freshly migrated schema, so a model added without
 * its migration fails a test instead of surfacing later as a missing table.
 */
class SchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<class-string<Model>> */
    private function applicationModels(): array
    {
        $classes = [];

        foreach (glob(base_path('app/Models/*.php')) as $file) {
            $classes[] = 'App\\Models\\'.basename($file, '.php');
        }
        foreach (['App\\User', 'App\\Admin', 'App\\VerifyUser'] as $class) {
            $classes[] = $class;
        }

        return array_values(array_filter(
            $classes,
            fn ($c) => class_exists($c) && is_subclass_of($c, Model::class)
        ));
    }

    public function test_every_model_has_a_table_the_migrations_create(): void
    {
        $missing = [];

        foreach ($this->applicationModels() as $class) {
            $table = (new $class)->getTable();

            if (! Schema::hasTable($table)) {
                $missing[] = class_basename($class)." -> '$table'";
            }
        }

        $this->assertSame([], $missing, "Models whose table no migration creates:\n  ".implode("\n  ", $missing));
    }

    public function test_the_guard_actually_inspects_models(): void
    {
        // Non-vacuity: an empty model list would make the assertion above pass
        // while checking nothing.
        $this->assertGreaterThan(15, count($this->applicationModels()));
    }
}
