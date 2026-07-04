<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bp_slider;

class SliderTableSeeder extends Seeder
{
    /**
     * Demo homepage sliders (matching database/sample-data.sql).
     *
     * @return void
     */
    public function run()
    {
        Bp_slider::truncate();

        // [name, image (committed placeholder), weight, description]
        $sliders = [
            ['Welcome to Beyond Plus CMS', 'slide-1.svg', 1, 'A modern, multi-language content management system.'],
            ['Manage Everything in One Place', 'slide-2.svg', 2, 'Posts, pages, menus, media and more — all from a clean admin panel.'],
        ];

        foreach ($sliders as [$name, $link, $weight, $desc]) {
            Bp_slider::insert([
                'slider_name'        => $name,
                'slider_link'        => $link,
                'slider_type'        => 'slider',
                'slider_weight'      => $weight,
                'slider_description' => $desc,
                'staff_id'           => 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}
