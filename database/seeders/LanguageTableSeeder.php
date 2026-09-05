<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            ['language_iso' => 'en', 'language_value' => 'English'],
            ['language_iso' => 'mm', 'language_value' => 'Myanmar'],
        ];
        foreach ($languages as $key => $value) {
            DB::table('bp_languages')->insert([
                'language_iso' => $languages[$key]['language_iso'],
                'language_value' => $languages[$key]['language_value'],
                'created_at' => Carbon::now(),
            ]);
        }
    }
}
