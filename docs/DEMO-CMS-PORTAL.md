# DOEH Merchant Activation Demo Portal — bp-cms.doehpos.com

The public demo surface for the CMS bridge: four **real, independent CMS
installations** — one per business vertical — behind a static landing page.
Each demo is byte-for-byte the system a merchant receives (D1: one install =
one merchant-shaped environment = one active theme). Nothing is mocked; orders
land in the DOEH **sandbox** plane.

This is demonstration infrastructure for Phase 5 (first merchant deployment).
It is **not** a merchant tenant and **never** carries production credentials.

## Topology

| Host | Role | Backing |
|---|---|---|
| `bp-cms.doehpos.com` | Landing / vertical selector | static page |
| `bp-cms-restaurant.doehpos.com` | Restaurant demo | `/opt/demo-cms-restaurant` · `doeh-restaurant` theme · :8894 |
| `bp-cms-retail.doehpos.com` | Retail demo | `/opt/demo-cms-retail` · `doeh-retail` theme · :8895 |
| `bp-cms-service.doehpos.com` | Service demo | `/opt/demo-cms-service` · `doeh-service` theme · :8896 |
| `bp-cms-business.doehpos.com` | Business demo | `/opt/demo-cms-business` · `doeh-business` theme · :8897 |

Single-level `bp-cms-*` names are deliberate: Cloudflare Universal SSL covers
only one subdomain level, and the naming keeps the platform namespace clearly
separate from merchant identities (a real merchant runs under their own
domain, never `bp-cms-*`).

Each instance: sqlite, `doeh-identity` (activated, **disabled** — sign-in demo
is a later enhancement), `doeh-commerce` + `doeh-commerce-storefront` active,
vertical theme active, its own sandbox `sk_test_` (shop 1, `orders:read` +
`orders:write`), rotated admin password. Serving: `demo-cms-<v>.service`
(php artisan serve, loopback) behind nginx `demo-cms.conf`.

## Environment boundary (the rule that must never move)

- Keys are **sandbox-minted** (`pos_site_sandbox`); orders go to
  `sandbox-api.doehpos.com` only.
- **Never** configure an `sk_live_` on a demo instance. A demo proves the
  product; it must not become a production customer.
- Admin logins are **not published**. The dashboard is shown by the operator
  during a sales call. Credentials: root-only on the host
  (`/etc/doeh-pos/demo-cms/admin-creds`).

## Nightly reset

`demo-cms-reset.timer` (03:15 UTC) restores each instance's sqlite DB from the
pristine snapshot in `/etc/doeh-pos/demo-cms/snapshots/` and clears runtime
caches/sessions. Visitors' demo orders and any admin-side edits vanish daily.

To make an intentional config change permanent: change it on the instance,
then re-copy `database/database.sqlite` over the snapshot.

## After a sandbox reset (known coupling)

A `pos_site_sandbox` deterministic reset (seed v6) **wipes the demo `sk_`
clients** — storefront checkouts then fail with `API_KEY_INVALID`. Recovery:

1. Re-mint per vertical (as deploy, against the sandbox config cache):
   `api-client:mint --shop=1 --name="Demo CMS (<v>) — bp-cms-<v>.doehpos.com"
   --target-env=test --scopes=orders:read,orders:write --all-branches --json`
   (the key is the `secret` field, show-once).
2. Install into the instance's `doeh-commerce` `secret_key` option.
3. Re-snapshot the instance DB so the nightly reset keeps the new key.
4. Update `/etc/doeh-pos/demo-cms/sk-clients` (client-id inventory).

Keys minted against the **prod** DB will not work here: the sandbox edge
validates against the sandbox DB.

## Go-live checklist (dark → public)

Everything host-side is staged and dark. Public exposure needs exactly:

1. Five DNS records (Cloudflare, **orange cloud** — origin :443 is
   Cloudflare-only): `bp-cms`, `bp-cms-restaurant`, `bp-cms-retail`,
   `bp-cms-service`, `bp-cms-business`.
2. A public walk of each storefront (home → store → cart → checkout →
   confirmation) and one operator dashboard check.

Config source of truth is the live host; copies are synced to the ops repo
(`serverconfig/lvl2`: `nginx/sites-enabled/demo-cms.conf`,
`systemd/demo-cms-*`, `scripts/demo-cms-reset.sh`).
