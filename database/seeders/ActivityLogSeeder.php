<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    /** A few human-readable dashboard activity entries, attributed to the demo admin. */
    public function run()
    {
        DB::table('activity_log')->truncate();

        $rows = [
            ['created the post “Welcome to Beyond Plus CMS”', 'created', 3, 96],
            ['created the page “About Us”', 'created', 4, 72],
            ['updated the post “Getting Started with the Admin Panel”', 'updated', 2, 48],
            ['created the event “Community meetup”', 'created', 9, 24],
            ['updated the page “Contact”', 'updated', 6, 6],
        ];

        foreach ($rows as [$description, $event, $subjectId, $hoursAgo]) {
            DB::table('activity_log')->insert([
                'log_name'     => 'content',
                'description'  => $description,
                'subject_type' => 'App\\Models\\Bp_post',
                'event'        => $event,
                'subject_id'   => $subjectId,
                'causer_type'  => 'App\\Admin',
                'causer_id'    => 1,
                'created_at'   => now()->subHours($hoursAgo),
                'updated_at'   => now()->subHours($hoursAgo),
            ]);
        }
    }
}
