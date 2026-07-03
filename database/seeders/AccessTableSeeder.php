<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bp_access;
use App\Models\Bp_module;

class AccessTableSeeder extends Seeder
{
    /**
     * Grant module access per role, matching sample-data.sql and what AdminAuth
     * checks (usertype + canshow). Roles: 1=user, 2=staff, 3=admin, 4=superadmin.
     * Superadmin sees every module; lower roles get everything except a few
     * settings/admin-only modules; a plain user sees none of the admin modules.
     */
    public function run()
    {
        Bp_access::truncate();

        // Modules staff (2) / admin (3) should not see (settings, users, reports…).
        $restricted = [7, 8, 9, 10, 15, 16];

        foreach (Bp_module::pluck('module_id') as $moduleId) {
            foreach ([1, 2, 3, 4] as $role) {
                $canShow = match (true) {
                    $role === 4 => 1,                                       // superadmin: everything
                    $role >= 2  => in_array($moduleId, $restricted, true) ? 0 : 1,
                    default     => 0,                                       // plain user: no admin modules
                };

                Bp_access::insert([
                    'module_id' => $moduleId,
                    'usertype'  => $role,
                    'canshow'   => $canShow,
                    'cancreate' => $role >= 2 ? 1 : 0,
                    'canedit'   => $role >= 2 ? 1 : 0,
                    'candelete' => $role >= 2 ? 1 : 0,
                ]);
            }
        }
    }
}
