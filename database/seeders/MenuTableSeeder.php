<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bp_menu;

class MenuTableSeeder extends Seeder
{
    /**
     * Demo navigation matching database/sample-data.sql:
     * a "Company" dropdown (About Us, Our Services) + a top-level Contact link,
     * linked to the demo pages seeded by PostTableSeeder.
     *
     * @return void
     */
    public function run()
    {
        Bp_menu::truncate();

        // [menu_id, name, link, post_id, weight, parent_id, type]
        $menus = [
            [1, 'Company',      '#',        0, 1, 0, 'custom'],
            [2, 'About Us',     'about-us', 4, 1, 1, 'default'],
            [3, 'Our Services', 'services', 5, 2, 1, 'default'],
            [4, 'Contact',      'contact',  6, 3, 0, 'default'],
        ];

        foreach ($menus as [$id, $name, $link, $postId, $weight, $parent, $type]) {
            Bp_menu::insert([
                'menu_id'      => $id,
                'menu_name'    => $name,
                'menu_link'    => $link,
                'post_id'      => $postId,
                'menu_weight'  => $weight,
                'menu_icon'    => '',
                'parent_id'    => $parent,
                'menu_type'    => $type,
                'staff_id'     => 1,
                'lang'         => 1,
                'translate_id' => '0',
                'created_at'   => now(),
            ]);
        }
    }
}
