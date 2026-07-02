<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use ILLuminate\Database\Eloquent\Model;
use App\Models\Bp_tax;

class TaxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     	Bp_tax::truncate();

        // Ids 1..3 (auto-increment resets on truncate) — referenced by post relationships.
        $categories = [
            ['Uncategorized', 'uncategorized', 'fa fa-list'],
            ['Announcements', 'announcements', 'fa fa-bullhorn'],
            ['Guides', 'guides', 'fa fa-book'],
        ];
        foreach ($categories as [$name, $link, $icon]) {
            Bp_tax::create([
                'tax_name'   => $name,
                'parent_id'  => '0',
                'tax_link'   => $link,
                'tax_icon'   => $icon,
                'tax_active' => 'yes',
                'tax_type'   => 'cat',
                'lang'       => 1,
                'created_at' => now(),
            ]);
        }
    }
}
