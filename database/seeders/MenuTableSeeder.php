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

        // [menu_id, name, link, post_id, weight, parent_id, type, lang, translate_id]
        // Myanmar rows (lang=2) translate the base rows via translate_id and are
        // re-parented to their Myanmar parent so the dropdown works in both langs.
        $menus = [
            [1, 'Company',      '#',        0, 1, 0, 'custom',  1, '0'],
            [2, 'About Us',     'about-us', 4, 1, 1, 'default', 1, '0'],
            [3, 'Our Services', 'services', 5, 2, 1, 'default', 1, '0'],
            [4, 'Contact',      'contact',  6, 3, 0, 'default', 1, '0'],
            [9, 'Blog',         'blog',     0, 4, 0, 'default', 1, '0'],
            [5, 'ကုမ္ပဏီ',              '#',        0, 1, 0, 'custom',  2, '1'],
            [6, 'ကျွန်ုပ်တို့အကြောင်း', 'about-us', 4, 1, 5, 'default', 2, '2'],
            [7, 'ဝန်ဆောင်မှုများ',      'services', 5, 2, 5, 'default', 2, '3'],
            [8, 'ဆက်သွယ်ရန်',          'contact',  6, 3, 0, 'default', 2, '4'],
            [10, 'ဘလော့ဂ်',            'blog',     0, 4, 0, 'default', 2, '9'],
        ];

        foreach ($menus as [$id, $name, $link, $postId, $weight, $parent, $type, $lang, $translateId]) {
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
                'lang'         => $lang,
                'translate_id' => $translateId,
                'created_at'   => now(),
            ]);
        }
    }
}
