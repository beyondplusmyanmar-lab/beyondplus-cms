# Theme development guide

Beyond Plus CMS front-end themes are self-contained folders under
`resources/views/theme/<slug>/`. A theme is a set of Blade templates that render
the public site; the active theme is stored in the `bp_options` table
(`option_name = 'theme'`, default `default`) and switched from the admin
**Themes** page.

> The CMS is the **secure host**: a theme is scanned, compatibility-checked and
> fingerprinted before it is made active — the same package model as plugins
> (see [plugin-development.md](plugin-development.md)). Design against this layout
> and a theme installs, activates and updates unchanged.

## Anatomy

A theme is **Blade only** — every view the front controller can render must
exist, so the theme works on every route without 404s.

```
resources/views/theme/my-theme/
├── theme.json              # manifest (required)
├── layouts/
│   ├── app.blade.php       # <html> shell: <head>, styles, header+footer includes
│   ├── header.blade.php    # site nav
│   └── footer.blade.php    # site footer (fire bp_do_action('theme_footer') here)
├── index.blade.php         # home page
├── blog.blade.php          # blog listing
├── single.blade.php        # one post / page
├── term.blade.php          # category (term) listing
├── search.blade.php        # search results
├── sidebar.blade.php       # shared sidebar partial
├── calendar.blade.php      # events calendar  (/events)
├── contact.blade.php       # contact form     (/contact)
├── faq.blade.php           # FAQ accordion    (/faq)
└── template/
    ├── contact.blade.php   # page template, selected by post_template = "contact"
    └── fullwidth.blade.php # page template, selected by post_template = "fullwidth"
```

Copy the `default` theme as a starting point — it implements every view.

## Manifest (`theme.json`)

```json
{
  "id": "my-theme",
  "type": "theme",
  "name": "My Theme",
  "description": "What it looks like and who it's for.",
  "version": "1.0.0",
  "author": "You",
  "homepage": "https://developers.beyondplus.com/themes/my-theme",
  "license": "MIT",
  "minCmsVersion": "2.0.0"
}
```

`id` should match the folder slug. `minCmsVersion` is checked before activation —
a theme that needs a newer CMS is **blocked**, not silently broken.

## Assets: CSS, JS, images, fonts

This is the part that trips people up, so it has one hard rule:

> **Only files under `public/` are web-served.** `resources/views/theme/<slug>/`
> is a Blade view directory — a browser **cannot** `GET` a `.css`, `.js`, `.png`
> or font from there. Anything referenced by `<link href>`, `<script src>`,
> `<img src>` or `url(...)` must resolve to a URL under `public/`.

There is **no** asset-publishing step, symlink or `theme_asset()` helper — assets
are served straight from `public/` by the web server. So you have two patterns:

### Pattern A — inline + CDN + shared public *(recommended, and what the shipped themes do)*

Keep the theme in its **one folder** with no separate asset files:

- **CSS** → an inline `<style>` block in `layouts/app.blade.php`.
- **JS** → an inline `<script>`, plus libraries (Bootstrap, jQuery, fonts) from a
  **CDN** `<link>` / `<script>`.
- **Images** → `asset('favicon.svg')` and other files already shipped in
  `public/` (`public/img/…`), or user-uploaded media via `bp_upload_url(...)`.

Benefits: the theme is fully covered by the integrity fingerprint, there is **no
build step**, and the repository stays lean.

### Pattern B — a per-theme public asset folder *(only when you truly need real files)*

If a theme ships a custom font file, a large stylesheet, a bundled JS file or its
own logo, put those under `public/`, keyed by slug — mirroring the existing
`public/theme-previews/` convention — while the Blade stays in `resources/`:

```
public/theme/<slug>/css/style.css      ← web-served static assets
public/theme/<slug>/js/theme.js
public/theme/<slug>/img/logo.svg
resources/views/theme/<slug>/…          ← blade templates (unchanged)
```

Reference them with `asset()`:

```blade
<link rel="stylesheet" href="{{ asset('theme/my-theme/css/style.css') }}">
<script src="{{ asset('theme/my-theme/js/theme.js') }}"></script>
<img src="{{ asset('theme/my-theme/img/logo.svg') }}" alt="Logo">
```

Two things to know when you choose Pattern B:

1. **The integrity check does not cover `public/`.** `Theme::fingerprint()` hashes
   only the `.php` templates + `theme.json` inside the theme folder. Assets under
   `public/` sit outside the tamper baseline — fine, just be aware the "Modified"
   badge won't track them.
2. **Keep it lean.** Optimise images and avoid committing large binaries; bloated
   per-theme asset folders are exactly what Pattern A exists to avoid.

### Preview thumbnail

The admin **Themes** page shows a thumbnail from
`public/theme-previews/<slug>.png` (falling back to a placeholder icon). Add a
~1280×800 PNG there so your theme has a card image.

## Rendering data

The front controller passes data into these views; pull the rest through the
CMS helpers (no direct DB access needed):

| Helper | Returns |
|---|---|
| `bp_post($limit)` | Latest published posts (with `translate`, `categories`, `creator`) |
| `bp_menu()` | The nav menu tree (`children`, `translate`, `menu_type`, `menu_link`) |
| `bp_tax()` | Categories for the sidebar |
| `bp_slider()` | Home-page slider entries (`slider_link`, `slider_name`, `slider_description`) |
| `site_information('blogname' \| 'blogdescription' \| 'admin_email')` | Site option row (use `optional(...)->option_value`) |
| `bp_option('key', 'default')` | Any single option (e.g. `faq_enabled`) |
| `bp_upload_url($path)` | Public URL for uploaded media (featured images, slides) |
| `bbParse($post->body)` | Renders post body HTML — wrap it in `.bp-content` |
| `bp_do_action('theme_footer')` | Lets active plugins inject into the footer |

## Conventions the shipped themes follow

- **Namespace your CSS** per theme (e.g. `.md-card`, `.nc-card`) so themes read as
  self-contained. Keep `.bp-content` (the wrapper around `bbParse()` output) and
  the `bp_*()` helpers as the shared contract — the editor's HTML is
  theme-agnostic and every theme should style `.bp-content`.
- **Bilingual (EN / MM).** Switch strings on `app()->getLocale() === 'mm'` and
  load **Noto Sans Myanmar** so Burmese renders. When a record has a `translate`
  relation for the active locale, swap to it (see any shipped view for the two-line
  pattern).
- **Responsive + accessible.** Mobile-first, visible `:focus-visible`, and respect
  `prefers-reduced-motion`.

## Security & lifecycle the host enforces

- **Static scan** — high-risk PHP (`eval`, shell exec, obfuscation, remote
  `include`, file deletion) **blocks activation**. Review from Themes → **Scan**.
  Inline `<script>` in Blade is stripped before scanning, so normal front-end JS is
  fine; just don't write dangerous PHP.
- **Compatibility** — `minCmsVersion` is checked before a theme goes active.
- **Integrity** — a SHA-256 baseline over the theme's `.php` + `theme.json` is
  stored on activate; changed templates show a **Modified** badge on the Themes
  page.
- **Verify in CI/cron** — `php artisan packages:verify` scans, tamper-checks and
  compatibility-checks every installed theme and plugin, exiting non-zero on any
  problem.

## Do / don't

- **Do** implement **every** view listed above, guard optional data
  (`optional(...)`, `bp_option(...)`), and keep the theme in one folder (Pattern A)
  unless you genuinely need shipped asset files.
- **Do** put any real static asset under `public/` — it is the only web-served
  root.
- **Don't** reference CSS/JS/images from inside `resources/views/theme/...` by URL;
  they won't load. And don't use `eval`, shell functions, obfuscation or remote
  `include` in a theme's PHP — the scanner blocks activation.
