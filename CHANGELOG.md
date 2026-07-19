# Changelog

## 2.7.0 — 2026-07-19

A front-end and dashboard refresh: the editorial themes get proper names and
richer homepages, a new Shopee-style storefront hero, theme-aware cover images,
and a rebuilt Bootstrap 5 admin dashboard — plus an XSS scan rule and a plugin
settings fix.

### Added
- **Theme-aware default cover** — the shared post placeholder now resolves per
  active theme (`public/theme-covers/<slug>.jpg`, light covers for the light
  themes, the dark aurora for Nocturne, a neutral fallback for the rest) so it
  never clashes with the theme. Resolved centrally in `bp_upload_url()`.
- **Security scan: reflected-XSS detection** — `PackageGuard::scan` now blocks
  activation of plugins/themes that echo raw request/superglobal data or emit it
  through unescaped Blade `{!! !!}`, and warns on any unescaped output. All
  shipped packages pass (0 criticals).

### Changed
- **Renamed the four editorial themes** — `bptheme1..4` →
  `meridian`/`nocturne`/`terra`/`pulse` (descriptive folder names). Installs
  with an old slug active must re-select the theme after upgrading.
- **Nocturne & Terra homepages rebuilt** — a lead-post spotlight hero with
  taxonomy chips + a closing CTA (Nocturne) and a calm lead feature over the
  hairline index (Terra), so both actually showcase the theme.
- **Storefront → Shopee look** — flash-sale bar with a live countdown, a hero
  carousel + promo tiles, trending search keywords, circular category tiles and
  a service/guarantee strip, all in the orange palette.
- **New dashboard experience** — upgraded the admin from Bootstrap 4-beta (Vali)
  to **Bootstrap 5.3.3** with a compat layer (so the ~90 views render
  unchanged), then redesigned forms and tables on the indigo theme: slate-blue
  tables, indigo Edit / soft-red Delete, soft theme-matched badges, enhanced
  multi-select checkbox lists, and a consistent spacing scale. Debranded the
  vendored `adminlte` folders to `bptheme` and trimmed `bower_components`
  (21 MB → 2.3 MB) by dropping dead layout variants + unused vendor assets.
- Renamed the default cover file `default.jpg` → `default-cover.jpg` (fresh URL,
  cache-safe) and repointed the seeds, schema default and admin previews.

### Fixed
- **Burmese typography across the editorial themes** — Latin letter-spacing and
  all-caps were pulling apart Myanmar glyph clusters; a `html[lang="mm"]` block
  neutralises tracking/case transforms (English rendering unchanged).
- **Plugin settings 500 on repeater fields** — the settings page/save now handle
  `type: repeater` (e.g. doeh-commerce-storefront's product list) instead of
  crashing on the array value.
- Admin tables: removed a stray dark line above the header (BS5 row border) and
  restored `.card-body` padding lost in the BS5 upgrade.

### Polished
- Font smoothing, smooth-scroll (reduced-motion-guarded) and lazy-loaded list
  images across the editorial themes; regenerated all theme preview images.

## 2.6.0

A backward-compatible feature release: the **DOEH bridge** — the CMS becomes a
storefront and identity front-end for the DOEH POS platform — plus four vertical
reference themes and merchant-activation tooling.

### Added
- **DOEH Identity plugin** (`doeh-identity`, 0.2.0) — hosted OAuth 2.1 + PKCE
  customer sign-in against the DOEH Identity Platform with a read-only loyalty
  dashboard; themes consume it through a header account slot and loyalty section.
- **DOEH Commerce plugin** (`doeh-commerce`, 0.1.0) — server-side connector to
  the DOEH Orders API; the CMS server holds the API key, the browser never sees
  it.
- **DOEH Commerce Storefront plugin** (`doeh-commerce-storefront`, 0.3.0) —
  reference checkout over the connector: catalogue → cart → a real DOEH order
  with confirmation, a fulfilment-preference selector (0.2.0), and a merchant
  **Orders dashboard** (0.3.0).
- **DOEH Setup plugin** (`doeh-setup`, 0.1.0) — a merchant setup wizard walking
  activation end-to-end (credentials, connection check, theme, first order).
- **Four DOEH vertical themes** — `doeh-restaurant`, `doeh-retail`,
  `doeh-service` and `doeh-business` — each wiring both DOEH plugins, with
  merchant-editable settings (identity, hours, delivery note, socials) across
  all four.
- **Plugin capability declarations and dependency enforcement** — plugins
  declare capabilities; plugins *and themes* can require them and refuse to
  activate when a dependency is missing. Commerce views are theme-overridable.
- **Live demo portal** — [bp-cms.doehpos.com](https://bp-cms.doehpos.com), one
  sandbox-backed demo per vertical theme, reset nightly.
- Docs: the **DOEH bridge extension contract** (v1, frozen), a theme design
  guide, a merchant pack (onboarding checklist + first-order acceptance
  walkthrough), merchant-deployment and demo-portal runbooks, and three working
  developer examples.

### Changed
- Bumped `Plugin::CMS_VERSION` to `2.6.0`.

### Fixed
- The order confirmation page is session-bound (IDOR fix; storefront 0.1.2).
- Currency-aware money display in the default order template (0.1.1) and an MMK
  money-display fix in themes.
- Storefront header/footer padding.

## 2.5.0

A backward-compatible feature release focused on commerce and localization.

### Added
- **Commerce plugin** — a product catalogue, plus **promotions** and **store
  locations** surfaced through theme slots, hooks and admin tabs.
- **Commerce-Checkout plugin** — cart, checkout and orders (no payment gateway),
  serving `/shop`, `/cart` and `/orders`.
- **Storefront theme** — a product-first shopping theme (Shopee-style) with a
  search+cart header, promo banner, category strip and product grids that fill
  from Commerce. Paired with a **Storefront Setup plugin** that seeds the
  Shop/Cart menu and a landing page on activation.
- **Business theme** — a general-purpose, option-driven business homepage (hero,
  services, about, why-choose-us, stats, testimonials, news, FAQ, contact) whose
  POS sections fill in automatically when a commerce plugin is installed.
- **Theme Customize page** — per-theme settings schema with seed-on-activate.
- Plugin routes can now **own front-end URLs**, so plugins add real storefront
  paths without touching core routing.
- **Localized plugin descriptions** (`description_mm`), documented in the plugin
  guide.

### Changed
- **Myanmar localization** across the admin — Dashboard, Posts, Pages, Menu,
  Media, Users, General / Configuration / System settings, the Plugins pages and
  the System flow page; the admin dashboard now defaults to the app locale (mm).
- **Docs translated to Myanmar** — the theme development guide, the plugin guide
  (linked from the README) and the plugin/theme portal doc.
- README refreshed — a 7-theme gallery, a storefront preview, and aligned
  commerce card colours; the System flow page now shows Commerce plus a catch-all.
- Bumped `Plugin::CMS_VERSION` to `2.5.0`.

### Fixed
- Added the missing `sitemap.xml` and RSS views, which previously 500'd on every
  theme.
- Storefront search now searches products (via `/shop`), and site search also
  matches translated (Myanmar) content.
- The repeater **Remove** button no longer overlaps the last field's label.

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
