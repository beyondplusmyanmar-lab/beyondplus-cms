# Plugin development guide

Beyond Plus CMS plugins are self-contained folders under `/plugins`. A plugin can
ship hooks, database schema, admin/front-end pages, and assets, and the CMS
manages its full lifecycle (install → activate → update → deactivate → uninstall)
with security, compatibility and recovery built in.

> The CMS is the **secure host**. A future official portal
> (`developers.beyondplus.com`) will be the distribution channel — see
> [plugin-portal.md](plugin-portal.md). Design your plugin against this manifest
> and it will publish there unchanged.

## Anatomy

```
plugins/my-plugin/
├── plugin.json          # manifest (required)
├── my-plugin.php        # main file — registers hooks (required)
├── migrations/          # plugin-owned schema (optional)
│   └── 2026_01_01_000001_create_my_table.php
├── routes.php           # front-end / admin routes (optional)
├── views/               # Blade views, namespaced my-plugin::name (optional)
├── assets/              # css/js/images (optional)
├── lang/                # translations (optional)
└── uninstall.php        # cleanup on uninstall (optional)
```

## Manifest (`plugin.json`)

```json
{
  "id": "my-plugin",
  "type": "plugin",
  "name": "My Plugin",
  "description": "What it does.",
  "version": "1.0.0",
  "author": "You",
  "homepage": "https://developers.beyondplus.com/plugins/my-plugin",
  "license": "MIT",
  "minCmsVersion": "2.0.0",
  "requires": { "php": "8.1", "extensions": ["curl"] },
  "permissions": ["http", "database"],
  "main": "my-plugin.php",
  "admin_menu": { "title": "My Plugin", "link": "my-plugin", "icon": "fa fa-cog", "parent": 8 }
}
```

| Field | Purpose |
|---|---|
| `id` / `type` / `name` / `version` | Stable package identity (shared with the portal) |
| `minCmsVersion`, `requires` | Compatibility — activation is **blocked** if unmet |
| `permissions` | Declares what the plugin does (informational today) |
| `main` | The file loaded on boot to register hooks |
| `admin_menu` | Adds a sidebar link + access grant to an admin page |

## Hooks

From your main file:

```php
// Action — side effect
bp_add_action('theme_footer', fn () => print '<p>Hi</p>');

// Filter — transform a value (must return it)
bp_add_filter('the_content', fn ($html) => $html.'<hr>');
```

Trigger your own from core/theme: `bp_do_action('name', ...$args)` /
`bp_apply_filters('name', $value, ...$args)`. Optional priority (lower first).

Provider example — implement a delivery channel:

```php
bp_add_filter('send_sms', function ($sent, $to, $message) {
    if ($sent) return $sent;                 // already handled
    // ...call your gateway...
    return true;                              // delivered
});
```

## Database

Ship Laravel migrations in `migrations/`. They run on **activate**, are skipped
if already applied (so updates only run new ones), kept on **deactivate**, and
rolled back on **uninstall**. Add `uninstall.php` for extra cleanup.

## Pages (routes + views)

`routes.php` (loaded only while active) — declare your own middleware:

```php
Route::middleware('admins')->prefix('bp-admin')->group(function () {
    Route::get('my-plugin', fn () => view('my-plugin::index'));
});
```

Views live in `views/` and render as `view('my-plugin::index')`. To make an
`/bp-admin/*` page reachable and show it in the sidebar, add `admin_menu` to the
manifest (the CMS registers the module + access on activate, removes it on
deactivate).

## Security & lifecycle the host enforces

- **Static scan** — high-risk code (`eval`, shell exec, obfuscation, remote
  include) **blocks activation**. Review from Plugins → Scan.
- **Compatibility** — `minCmsVersion` / `requires` checked before activation.
- **Integrity** — a SHA-256 baseline is stored on activate; modified files are
  flagged "Modified" on the Plugins page.
- **Recovery** — a plugin that throws on load is auto-disabled and reported, so
  it can't take down the site. Keep your main file side-effect-light and defensive.
- **Permissions** — only admins with Plugins-module access can manage plugins;
  every action is audit-logged.

## Do / don't

- **Do** guard for missing tables/config (`Schema::hasTable`, `bp_option(...)`),
  fail quietly, and keep boot fast.
- **Don't** use `eval`, shell functions, obfuscation, or remote `include` — the
  scanner blocks them and they'll be rejected by the portal.
