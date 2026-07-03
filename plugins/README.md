# Plugins

Hook-based plugins for Beyond Plus CMS. Each plugin is a folder in here with:

- `plugin.json` — metadata (`name`, `description`, `version`, `author`, `main`)
- a main PHP file (default `<slug>.php`) that registers hooks

Activate / deactivate from the admin: **bp-admin → Plugins**. Active plugins are
stored in the `active_plugins` option and their main file is loaded on boot.

## Hooks

Register these from a plugin's main file:

- **Actions** (side effects): `bp_add_action('hook', fn (...$args) => ...)`,
  triggered by `bp_do_action('hook', ...$args)`
- **Filters** (transform a value): `bp_add_filter('hook', fn ($value, ...$args) => $value)`,
  triggered by `bp_apply_filters('hook', $value, ...$args)`

Both accept an optional priority (lower runs first, default `10`).

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
