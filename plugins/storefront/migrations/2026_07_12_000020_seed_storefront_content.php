<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the Storefront theme's declared pages + menu (from its theme.json "seed"
 * block) on activate, and removes them on uninstall. Idempotent: existing pages
 * / menu items with the same slug/label are left untouched.
 */
return new class extends Migration
{
    /** Read the seed spec the active Storefront theme declares. */
    private function seed(): array
    {
        $seed = \App\Support\Theme::meta('storefront')['seed'] ?? [];
        return is_array($seed) ? $seed : [];
    }

    public function up(): void
    {
        $seed = $this->seed();

        foreach ($seed['pages'] ?? [] as $page) {
            $slug = trim($page['slug'] ?? '');
            if ($slug === '') {
                continue;
            }
            $exists = DB::table('bp_posts')->where('post_link', $slug)->where('post_type', 'page')->exists();
            if ($exists) {
                continue;
            }
            DB::table('bp_posts')->insert([
                'title'         => $page['title'] ?? ucfirst($slug),
                'body'          => $page['body'] ?? '',
                'post_link'     => $slug,
                'post_type'     => 'page',
                'post_template' => $page['template'] ?? 'default',
                'post_active'   => 'yes',
                'lang'          => 1,
                'staff_id'      => 1,
                'featured_img'  => 'default-cover.jpg',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $weight = (int) (DB::table('bp_menus')->max('menu_weight') ?? 0);
        foreach ($seed['menu'] ?? [] as $item) {
            $label = trim($item['label'] ?? '');
            $link  = $item['link'] ?? '';
            if ($label === '') {
                continue;
            }
            $exists = DB::table('bp_menus')->where('menu_name', $label)->where('menu_link', $link)->exists();
            if ($exists) {
                continue;
            }
            // If the link matches a page slug, point the menu at that page so the
            // front controller (menu() → Bp_post by post_id) resolves it.
            $pageId = (int) (DB::table('bp_posts')->where('post_link', $link)->where('post_type', 'page')->value('id') ?? 0);
            DB::table('bp_menus')->insert([
                'menu_name'    => $label,
                'menu_link'    => $link,
                'post_id'      => $pageId,
                'menu_type'    => $item['type'] ?? 'default',
                'menu_weight'  => ++$weight,
                'parent_id'    => 0,
                'lang'         => 1,
                'staff_id'     => 1,
                'translate_id' => '0',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        $seed = $this->seed();

        foreach ($seed['pages'] ?? [] as $page) {
            $slug = trim($page['slug'] ?? '');
            if ($slug !== '') {
                DB::table('bp_posts')->where('post_link', $slug)->where('post_type', 'page')->delete();
            }
        }
        foreach ($seed['menu'] ?? [] as $item) {
            $label = trim($item['label'] ?? '');
            if ($label !== '') {
                DB::table('bp_menus')->where('menu_name', $label)->where('menu_link', $item['link'] ?? '')->delete();
            }
        }
    }
};
