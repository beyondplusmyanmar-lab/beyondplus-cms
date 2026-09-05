<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

/**
 * No model may expose its primary key or its timestamps to mass assignment.
 *
 * The admin controllers build their payload as $inputs = $request->all() and
 * hand it straight to create() or update(), so $fillable is the only thing
 * deciding which columns a request can reach. Thirteen models listed their own
 * primary key or created_at/updated_at there, which let a crafted request
 * choose a row's id or forge its audit timestamps.
 *
 * This does not make $request->all() safe in general - a request can still set
 * any legitimately fillable column, whether or not the form shows it - but it
 * closes the columns that are never a form field, and it does so at the model
 * layer, so it holds for every caller rather than for 26 controller actions
 * one at a time.
 *
 * Timestamps are written by Eloquent, and where the application sets them
 * deliberately it does so through DB::table()->insert(), which bypasses
 * $fillable entirely.
 */
class MassAssignmentTest extends TestCase
{
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

    public function test_no_model_exposes_its_primary_key_or_timestamps(): void
    {
        $problems = [];

        foreach ($this->applicationModels() as $class) {
            $model = new $class;
            $fillable = $model->getFillable();

            if ($fillable === []) {
                continue; // guarded another way, or not mass-assigned at all
            }

            if (in_array($model->getKeyName(), $fillable, true)) {
                $problems[] = class_basename($class)." exposes its primary key '{$model->getKeyName()}'";
            }

            foreach (['created_at', 'updated_at'] as $timestamp) {
                if (in_array($timestamp, $fillable, true)) {
                    $problems[] = class_basename($class)." exposes '$timestamp'";
                }
            }
        }

        $this->assertSame([], $problems, "Models exposing keys or timestamps to mass assignment:\n  ".implode("\n  ", $problems));
    }

    public function test_the_guard_actually_inspects_models(): void
    {
        // Non-vacuity: an empty model list would make the assertion above pass
        // while checking nothing.
        $withFillable = array_filter(
            $this->applicationModels(),
            fn ($c) => (new $c)->getFillable() !== []
        );

        $this->assertGreaterThan(12, count($withFillable));
    }
}
