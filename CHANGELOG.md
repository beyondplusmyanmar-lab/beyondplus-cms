# Changelog

## 2.4.0

A backward-compatible release focused on the front-end theme system.

### Added
- **Five distinct default themes**, each self-contained (Bootstrap 5 + fonts via
  CDN, no build step) and complete with every front-end view so any can be
  activated safely: **Aurora** (`default`, clean teal), **Meridian** (`bptheme1`,
  editorial magazine), **Nocturne** (`bptheme2`, dark glassmorphism), **Terra**
  (`bptheme3`, minimal), and **Pulse** (`bptheme4`, bold gradient). All bilingual
  (mm/en) with Noto Sans Myanmar, and each with an admin preview thumbnail.
- **Theme development guide** ([docs/theme-development.md](docs/theme-development.md))
  — folder/view layout, the `theme.json` manifest, the data helpers, and where
  theme assets belong (only `public/` is web-served).
- **Core update service** — `CoreUpdate` checks the project's GitHub releases
  against the running version and surfaces "update available" with the release
  notes; a Configuration → **System** page shows CMS / PHP / Laravel versions and
  the update status. Check-and-announce only; applying an update stays out-of-band
  and every call degrades gracefully.

### Changed
- Rebuilt `bptheme1`/`bptheme2` as the complete Meridian and Nocturne themes
  (slugs kept for back-compat); each theme now namespaces its own CSS while the
  `.bp-content` wrapper and `bp_*()` helpers stay shared.
- Bumped `Plugin::CMS_VERSION` to `2.4.0`; migrated `phpunit.xml` to the
  PHPUnit 12 schema.

### Fixed
- Activating `bptheme1`/`bptheme2` previously 404'd `/events`, `/faq` and
  `/contact` because those themes lacked the calendar/contact/faq/template views.
  Both are now complete, guarded by a data-provider test that renders every front
  route against all five themes.

## 2.3.0

A large, backward-compatible feature release.

### Added
- **Activity log** — human-readable audit trail (content, media, menu, plugin,
  and auth: successful **and** failed logins), a dashboard feed, a filterable
  Reports → Activity page, CSV export, and scheduled auto-pruning.
- **Events calendar** — month view in the admin (`/bp-admin/news/calendar`) and
  a public one (`/events`); events now carry a date **and** time.
- **FAQ** and **Feedback** modules — admin CRUD, on/off toggles, a public `/faq`
  accordion, and a merged, spam-protected `/contact` form (honeypot + rate limit).
- **Front-end search** — header search box + results page.
- **Blog** listing (`/blog`) with category badges on cards and posts.
- **System flow** page — an n8n-style map of how services route through plugins.
- **Telegram Feedback** plugin — demo of connecting to an external service via
  the hook system (`feedback_received`).
- **`make install`** — one-click setup (deps, env, key, permissions, database).
- Themed, bilingual (mm/en) **error pages** with a developer log for admins /
  allow-listed IPs.

### Changed / Security
- **Hardened admin login** — decoy login path, per-IP rate limiting, trusted
  proxies, and a developer-IP allow-list.
- **Package scanner** — file-deletion is now a blocking (critical) finding; the
  uninstall script is re-scanned before it runs.
- `bp_options.option_name` widened to `varchar(191)` + made unique.
- Removed Laravel branding from the public theme; log file renamed to
  `beyondplus.log`.

### Fixed
- Config/settings save no longer 500s on empty fields (null coalescing) and is
  now atomic.
- Admin module hierarchy is consistent across both DB-setup paths, guarded by a
  test so they can't silently drift.

## 2.2.0

- Initial public release: multi-language CMS, admin panel, theme system, secure
  plugin/theme host, and a token-authenticated JSON API.
