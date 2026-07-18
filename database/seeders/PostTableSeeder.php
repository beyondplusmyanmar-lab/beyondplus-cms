<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bp_post;
use App\Models\Bp_relationship;

class PostTableSeeder extends Seeder
{
    /**
     * Seeds the same demo content as database/sample-data.sql:
     * three posts (Uncategorised) and three pages that demonstrate the
     * default / full-width / contact page templates.
     *
     * @return void
     */
    public function run()
    {
        Bp_post::truncate();
        Bp_relationship::truncate();

        // Posts (newest id shows first via the front-end ordering).
        $posts = [
            [3, 'Welcome to Beyond Plus CMS', '<p>Beyond Plus CMS is a lightweight, multi-language content management system built on Laravel. This sample post shows how the front-end theme renders content out of the box.</p>', 'welcome-to-beyond-plus-cms', 1],
            [2, 'Getting Started with the Admin Panel', '<p>The admin panel lives at <code>/bp-admin</code>. From there you can manage posts, pages, menus, media, sliders and site settings.</p>', 'getting-started-admin-panel', 3],
            [1, 'Building Multilingual Content', '<p>Every post and menu item can have a translation. Switch the site locale and the matching translated content is served automatically.</p>', 'building-multilingual-content', 5],
        ];
        foreach ($posts as [$id, $title, $body, $link, $daysAgo]) {
            Bp_post::insert([
                'id' => $id, 'title' => $title, 'body' => $body, 'featured_img' => 'default-cover.jpg',
                'post_link' => $link, 'post_type' => 'post', 'post_template' => 'default',
                'post_active' => 'yes', 'translate_id' => 0, 'lang' => 1, 'staff_id' => 1,
                'created_at' => now()->subDays($daysAgo), 'updated_at' => now()->subDays($daysAgo),
            ]);
            Bp_relationship::insert(['tax_id' => 1, 'post_id' => $id, 'type' => 'cat']);
        }

        // Pages demonstrating page-template usage.
        $pages = [
            [4, 'About Us', '<p>Beyond Plus CMS is a lightweight, multi-language content management system built on Laravel. This About page uses the <strong>default</strong> page template (with sidebar).</p>[block]1[/block]', 'about-us', 'default'],
            [5, 'Our Services', '<p>This page uses the <strong>full-width</strong> template (no sidebar), selected via the page template option in the admin.</p>', 'services', 'fullwidth'],
            [6, 'Contact', '<p>Reach out using the details on the right. This page uses the <strong>contact</strong> template.</p>', 'contact', 'contact'],
        ];
        foreach ($pages as [$id, $title, $body, $link, $template]) {
            Bp_post::insert([
                'id' => $id, 'title' => $title, 'body' => $body, 'featured_img' => 'default-cover.jpg',
                'post_link' => $link, 'post_type' => 'page', 'post_template' => $template,
                'post_active' => 'yes', 'translate_id' => 0, 'lang' => 1, 'staff_id' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Extra category memberships for browsing (Announcements=2, Guides=3).
        Bp_relationship::insert([
            ['tax_id' => 2, 'post_id' => 3, 'type' => 'cat'],
            ['tax_id' => 3, 'post_id' => 2, 'type' => 'cat'],
            ['tax_id' => 3, 'post_id' => 1, 'type' => 'cat'],
        ]);

        // News and events (consumed by /api/m/news and the SPA example).
        $news = [
            [7, 'Beyond Plus CMS v2 released', '<p>The latest release brings a rebuilt admin, a JSON API for the mobile app, and a themes manager.</p>', 'beyond-plus-cms-v2-released', 'news', null],
            [8, 'Scheduled maintenance this weekend', '<p>The service will be briefly unavailable on Sunday morning for planned upgrades.</p>', 'scheduled-maintenance', 'news', null],
            [9, 'Community meetup', '<p>Join our online community meetup to learn about building sites with Beyond Plus CMS.</p>', 'community-meetup', 'event', '2026-08-15 18:00:00'],
        ];
        foreach ($news as [$id, $title, $body, $link, $type, $eventAt]) {
            Bp_post::insert([
                'id' => $id, 'title' => $title, 'body' => $body, 'featured_img' => 'default-cover.jpg',
                'post_link' => $link, 'post_type' => $type, 'event_at' => $eventAt, 'post_template' => 'default',
                'post_active' => 'yes', 'translate_id' => 0, 'lang' => 1, 'staff_id' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
