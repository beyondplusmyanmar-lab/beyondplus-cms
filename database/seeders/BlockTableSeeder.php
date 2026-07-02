<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bp_block;

class BlockTableSeeder extends Seeder
{
    /**
     * Demo content block (matching database/sample-data.sql). Embedded in the
     * About Us page via the [block]1[/block] shortcode.
     *
     * @return void
     */
    public function run()
    {
        Bp_block::truncate();

        Bp_block::insert([
            'id'           => 1,
            'title'        => 'Why choose Beyond Plus CMS',
            'body'         => 'Fast, multi-language, and easy to manage — everything you need to publish content, right out of the box.',
            'block_url'    => 'why-choose',
            'block_type'   => 'content',
            'block_active' => 'yes',
            'translate_id' => 0,
            'lang'         => 1,
            'staff_id'     => 1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
