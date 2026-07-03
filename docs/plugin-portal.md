# Plugin & theme portal — architecture and roadmap

The CMS is designed to be an excellent, secure **package host** now, and to
consume packages from a future **official portal** without architectural change.
The portal is an ecosystem service, not part of every installation.

```
Beyond Plus CMS
    → Plugin Manager  (install · scan · verify · migrate · activate · audit)
    → Official Portal (future distribution channel)
```

## Phase 1 — local packages (shipped)

The CMS hosts plugins from the local `/plugins` directory with a full, safe
lifecycle and no internet dependency:

- discover / activate / deactivate / uninstall
- static **security scan** (blocks high-risk code)
- **compatibility** checks (`minCmsVersion`, PHP, extensions)
- plugin-owned **migrations** (run on activate, rolled back on uninstall)
- **integrity** baseline (SHA-256) + tamper detection
- **recovery mode** (a crashing plugin auto-disables, site stays up)
- **permission** gating + **audit** log

Themes are hosted the **same way** (implemented): each lives in
`resources/views/theme/<slug>` with a `theme.json` manifest, and the shared
`App\Support\PackageGuard` gives them the same **security scan** (Blade-aware —
inline `<script>` and comments are ignored), **compatibility** check and
**integrity** fingerprint. A theme that fails the scan is never made active.

## Phase 2 — official portal (future, separate service)

`developers.beyondplus.com` would provide one ecosystem for **two package types**
(`plugin` and `theme`) sharing all infrastructure:

```
Official Portal
├── Catalog (plugins + themes)
├── Developer accounts / Publisher dashboard
├── Docs + SDK
├── Release management + version history
├── Reviews / changelogs / security advisories
├── Signing (every release signed)
└── API (consumed by the CMS)
```

### Signed releases — the trust upgrade

Heuristic scanning is a safety net, not proof of authenticity. The portal signs
every published release so the CMS can verify provenance:

```
Developer → upload → portal scans → portal SIGNS release
CMS → download → verify signature → verify checksum → install
```

A release bundle carries a signature the CMS checks against the portal's public
key before install:

```
my-plugin-1.2.0.zip
├── plugin.json
├── signature.json      # signature over the package hash
└── ...
```

### CMS-side integration (small, additive)

Because packages already have **stable metadata** (`id`, `type`, `version`,
`minCmsVersion`, `requires`, …), the portal API can serve the same shape the CMS
already reads. Adding a portal only introduces a new *source*, not new internals:

- `plugin_registry_url` option → "Browse official plugins/themes"
- `GET {registry}/api/packages?type=plugin` → catalog
- download → **verify signature** → unpack to `/plugins` → run the existing
  activate lifecycle
- "update available" via version comparison; **safe updates** (verify → extract
  to temp → migrate → swap atomically → health-check → commit, else roll back)

## Deliberately not built yet

Marketplace UI, remote install/update, and signature verification wait for the
portal to exist. The CMS keeps the **enforcement** responsibilities (scan,
verify, lifecycle, migrations, audit, recovery); the portal is the **distribution
channel**.

## The honest boundary

PHP plugins run in the CMS process and therefore have its privileges — they
cannot be fully sandboxed short of process/container isolation. Signatures,
scanning, integrity and review reduce risk; they are not a complete security
boundary. So the guidance remains: **install only from trusted sources, prefer
signed releases, keep `/plugins` read-only to the web user, and review updates.**
