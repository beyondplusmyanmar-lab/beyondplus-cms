# Plugins

Hook-based plugins for Beyond Plus CMS. Each plugin is a folder in here with:

- `plugin.json` — manifest (`id`, `type`, `name`, `version`, `author`,
  `homepage`, `license`, `minCmsVersion`, `requires`, `permissions`, `main`)
- a main PHP file (default `<slug>.php`) that registers hooks

Activate / deactivate from the admin: **bp-admin → Plugins**. Active plugins are
stored in the `active_plugins` option and their main file is loaded on boot.

**Full developer guide:** [`docs/plugin-development.md`](../docs/plugin-development.md).
**Portal roadmap:** [`docs/plugin-portal.md`](../docs/plugin-portal.md).

## Security

Plugins run real PHP, so a malicious or careless plugin is a genuine risk. The
core adds several layers — but **the most important rule is: only install
plugins from sources you trust.** No scanner can fully sandbox PHP.

- **Static security scan** — before a plugin is activated its PHP files are
  scanned for high-risk constructs (`eval`, `shell_exec`/`system`/backticks,
  `assert`/`create_function`, `preg_replace /e`, obfuscated `eval(base64_decode(...))`,
  remote `include`). **Any critical match blocks activation.** Lower-risk
  patterns (`base64_decode`, filesystem writes, `curl_exec`, …) are shown as
  warnings. Review a plugin any time from **Plugins → Scan**.
- **Compatibility checks** — `minCmsVersion` and `requires` (PHP version,
  extensions) are verified before activation; incompatible plugins are refused.
- **Integrity checking** — a SHA-256 baseline is stored on activate; a plugin
  whose files change afterwards is flagged **Modified** on the Plugins page.
- **Recovery mode** — a plugin that throws while loading is auto-disabled and
  reported (Plugins page + log), so one bad plugin can't take the site down.
- **Permission-gated** — every plugin action (including the POST
  activate/deactivate/uninstall) requires access to the Plugins module, so a
  lower-privilege admin can't manage plugins.
- **Audit log** — activate/deactivate/uninstall and blocked activations are
  written to the application log with the acting admin.
- **Not web-served** — the `/plugins` directory is outside `public/`, so plugin
  source is never directly reachable over HTTP.
- **Fail-safe boot** — a plugin that throws on load is caught and skipped rather
  than taking down the site.

Production hardening: make `/plugins` read-only to the web user so a
compromised app can't drop or modify plugin code at runtime, and keep plugin
code under review/version control.

## Hooks

Register these from a plugin's main file:

- **Actions** (side effects): `bp_add_action('hook', fn (...$args) => ...)`,
  triggered by `bp_do_action('hook', ...$args)`
- **Filters** (transform a value): `bp_add_filter('hook', fn ($value, ...$args) => $value)`,
  triggered by `bp_apply_filters('hook', $value, ...$args)`

Both accept an optional priority (lower runs first, default `10`).

## Database (plugin-owned migrations)

A plugin owns its schema. Ship Laravel migrations in a `migrations/` folder:

```
plugins/loyalty/
├── plugin.json
├── loyalty.php
├── migrations/
│   └── 2026_06_10_000001_create_loyalty_table.php
└── uninstall.php        (optional)
```

Lifecycle (the core runs this for you):

| Action | What happens |
|---|---|
| **Activate** | the plugin's pending migrations run (`up`) — its tables are created |
| **Update** | dropping in new migration files + reactivating runs only the new ones (Laravel's migration history skips those already applied) |
| **Deactivate** | the plugin is turned off but **its data/tables are kept** |
| **Uninstall** | migrations are rolled back (`down`, tables dropped), then an optional `uninstall.php` runs for any remaining cleanup |

## UI (routes, views & admin menu)

A plugin can ship its own pages:

```
plugins/logbook/
├── plugin.json
├── logbook.php
├── routes.php          # loaded only while active
├── views/
│   └── report.blade.php
└── migrations/
```

- **routes.php** — define routes with your own middleware (`web` for front-end
  pages, `admins` for admin pages). Loaded while the plugin is active.
- **views/** — registered as a namespace: render with `view('<slug>::name')`
  (e.g. `view('logbook::report')`).
- **admin_menu** in `plugin.json` — adds a sidebar link + access grant so an
  `/bp-admin/<link>` page is reachable and appears under Settings:

  ```json
  "admin_menu": { "title": "Logbook", "link": "logbook", "icon": "fa fa-book", "parent": 8 }
  ```

  The menu/access is added on activate and removed on deactivate. (When route
  caching is enabled in production, re-run `route:cache` after toggling plugins.)

### Hook points in core / themes

| Hook | Type | Where |
|---|---|---|
| `theme_footer` | action | end of the front-end footer |
| `admin_notices` | action | top of the admin content area |
| `the_content` | filter | post/page body before output |

## Example

```php
// plugins/sample-banner/sample-banner.php
bp_add_action('theme_footer', function () {
    echo '<div class="text-center small">Powered by my plugin</div>';
});
```
