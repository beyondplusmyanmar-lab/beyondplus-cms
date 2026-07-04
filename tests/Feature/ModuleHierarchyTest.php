<?php

namespace Tests\Feature;

use App\Models\Bp_module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards against the two DB-setup paths drifting: after seeding, every module's
 * parent must resolve to the expected parent by module_link (not a hard-coded id).
 */
class ModuleHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_module_hierarchy_is_consistent(): void
    {
        $linkById = Bp_module::pluck('module_link', 'module_id');

        // child module_link => expected parent module_link
        $expected = [
            'configuration'           => 'settings',
            'themes'                  => 'settings',
            'plugins'                 => 'settings',
            'permission'              => 'settings',
            'general'                 => 'settings',
            'account'                 => 'settings',
            'post/create'             => 'post',
            'category'                => 'post',
            'block'                   => 'post',
            'news'                    => 'post',
            'activity'                => 'reports',
            'reports/customer-report' => 'reports',
        ];

        foreach ($expected as $child => $parent) {
            $module = Bp_module::where('module_link', $child)->first();
            $this->assertNotNull($module, "module '{$child}' should exist");
            $this->assertSame(
                $parent,
                $linkById[$module->parent_id] ?? null,
                "module '{$child}' should be nested under '{$parent}'"
            );
        }
    }
}
