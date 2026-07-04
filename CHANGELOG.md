# Changelog

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
