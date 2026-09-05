<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Every Eloquent relation must name keys that exist as real columns.
 *
 * This class of bug has bitten twice here. Bp_comment::users() pointed at
 * App\User while comment authors are Customers, and because the theme wrapped
 * the author in @if, every comment silently rendered as nothing at all. Two
 * more relations named columns that do not exist: Bp_post::category() inferred
 * a 'category_tax_id' that bp_posts never had, and Bp_tax::post() named a
 * 'post_id' that is not on bp_taxes.
 *
 * None of it fails at boot. A relation is only resolved when something calls
 * it, so a wrong key surfaces as a query error or a silent null long after the
 * change that caused it.
 *
 * Relations to models outside App\ are skipped: those come from framework
 * traits (Notifiable's notifications() targets DatabaseNotification and the
 * notifications table, which this application does not publish).
 */
class RelationIntegrityTest extends TestCase
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

    /** @return list<array{string, Relation}> */
    private function relations(): array
    {
        $found = [];

        foreach ($this->applicationModels() as $class) {
            $model = new $class;

            foreach ((new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getNumberOfParameters() > 0 || $method->isStatic()) {
                    continue;
                }
                if (! str_starts_with($method->class, 'App\\')) {
                    continue;
                }
                try {
                    $relation = $model->{$method->name}();
                } catch (\Throwable) {
                    continue;
                }
                if (! $relation instanceof Relation) {
                    continue;
                }
                // Framework-owned relations (e.g. Notifiable) are not ours to guard.
                if (! str_starts_with(get_class($relation->getRelated()), 'App\\')) {
                    continue;
                }
                $found[] = [class_basename($class).'::'.$method->name.'()', $relation, $model];
            }
        }

        return $found;
    }

    public function test_every_relation_names_columns_that_exist(): void
    {
        $problems = [];

        foreach ($this->relations() as [$label, $relation, $model]) {
            $table = $model->getTable();
            $relatedTable = $relation->getRelated()->getTable();

            foreach ([$table, $relatedTable] as $t) {
                if (! Schema::hasTable($t)) {
                    $problems[] = "$label -> table '$t' does not exist";

                    continue 2;
                }
            }

            if ($relation instanceof HasOneOrMany) {
                $foreign = last(explode('.', $relation->getForeignKeyName()));
                if (! Schema::hasColumn($relatedTable, $foreign)) {
                    $problems[] = "$label -> foreign key '$foreign' is not a column on '$relatedTable'";
                }
                if (! Schema::hasColumn($table, $relation->getLocalKeyName())) {
                    $problems[] = "$label -> local key '{$relation->getLocalKeyName()}' is not a column on '$table'";
                }
            } elseif ($relation instanceof BelongsTo) {
                if (! Schema::hasColumn($table, $relation->getForeignKeyName())) {
                    $problems[] = "$label -> foreign key '{$relation->getForeignKeyName()}' is not a column on '$table'";
                }
                if (! Schema::hasColumn($relatedTable, $relation->getOwnerKeyName())) {
                    $problems[] = "$label -> owner key '{$relation->getOwnerKeyName()}' is not a column on '$relatedTable'";
                }
            } elseif ($relation instanceof BelongsToMany) {
                if (! Schema::hasTable($relation->getTable())) {
                    $problems[] = "$label -> pivot table '{$relation->getTable()}' does not exist";
                }
            }
        }

        $this->assertSame([], $problems, "Relations naming keys that do not exist:\n  ".implode("\n  ", $problems));
    }

    public function test_the_guard_actually_inspects_relations(): void
    {
        // Non-vacuity: an empty relation list would make the assertion above
        // pass while checking nothing.
        $this->assertGreaterThan(10, count($this->relations()));
    }
}
