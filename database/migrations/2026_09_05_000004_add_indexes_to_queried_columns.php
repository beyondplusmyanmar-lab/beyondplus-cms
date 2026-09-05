<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index the columns this application actually filters and joins on.
 *
 * Almost every table carried nothing but its primary key, including the ones
 * on the hottest paths: bp_menus.menu_link and bp_posts.post_link are looked
 * up on every front-end page view (the "/{name}" catch-all resolves through
 * menu() and falls through to detail()), bp_modules is queried on every admin
 * page to build the sidebar, and bp_relationships is a pivot that had an index
 * on neither of its foreign keys.
 *
 * Each entry below corresponds to a query that exists in the codebase; nothing
 * here is speculative. Columns were taken from the where/join/orderBy calls in
 * app/, resources/views/ and plugins/.
 *
 * Every index is guarded three ways - table exists, column exists, index not
 * already present - so this is safe to run against a deployment whose schema
 * was built by hand and may already carry some of them.
 *
 * Note for large tables: on MySQL, adding an index copies or locks the table
 * for the duration. customers and bp_posts are the ones likely to be big
 * enough to notice.
 */
return new class extends Migration
{
    /**
     * table => [index name => columns]
     *
     * @var array<string, array<string, list<string>>>
     */
    private array $indexes = [
        // Front-end: single-post lookup by slug, and the listing filters.
        'bp_posts' => [
            'bp_posts_post_link_index' => ['post_link'],
            'bp_posts_post_type_translate_id_index' => ['post_type', 'translate_id'],
            'bp_posts_lang_index' => ['lang'],
        ],
        // Front-end: the "/{name}" catch-all resolves menu_link on every request.
        'bp_menus' => [
            'bp_menus_menu_link_index' => ['menu_link'],
            'bp_menus_parent_id_index' => ['parent_id'],
        ],
        // Pivot between posts and taxonomies; had neither foreign key indexed.
        'bp_relationships' => [
            'bp_relationships_post_id_tax_id_index' => ['post_id', 'tax_id'],
            'bp_relationships_tax_id_index' => ['tax_id'],
        ],
        'bp_taxes' => [
            'bp_taxes_tax_type_index' => ['tax_type'],
            'bp_taxes_tax_link_index' => ['tax_link'],
            'bp_taxes_translate_id_index' => ['translate_id'],
        ],
        // Bp_post::comment() loads by post_id.
        'bp_comment' => [
            'bp_comment_post_id_index' => ['post_id'],
        ],
        // slidebar(): where parent_id = 0 and section = 1, on every admin page.
        'bp_modules' => [
            'bp_modules_parent_id_section_index' => ['parent_id', 'section'],
        ],
        'bp_access' => [
            'bp_access_module_id_index' => ['module_id'],
        ],
        // Customer sign-in, OTP verification and password reset all look the
        // account up by one of these.
        'customers' => [
            'customers_email_index' => ['email'],
            'customers_phone_index' => ['phone'],
        ],
        // Had no index at all, not even on the key it is joined by.
        'verify_users' => [
            'verify_users_user_id_index' => ['user_id'],
        ],
        // Public FAQ page: where is_active = 1 order by sort_order.
        'faqs' => [
            'faqs_is_active_sort_order_index' => ['is_active', 'sort_order'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $name => $columns) {
                if (! Schema::hasColumns($table, $columns)) {
                    continue;
                }
                if (Schema::hasIndex($table, $name)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                    $t->index($columns, $name);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $name => $columns) {
                if (! Schema::hasIndex($table, $name)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $t) use ($name) {
                    $t->dropIndex($name);
                });
            }
        }
    }
};
