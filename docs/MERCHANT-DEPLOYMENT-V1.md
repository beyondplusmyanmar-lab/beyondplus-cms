# Merchant Deployment v1 — runbook

The manual, end-to-end path for putting **one real merchant** on a DOEH CMS
storefront (Activation v1 **Phase 5**,
[CMS-MERCHANT-ACTIVATION-V1.md](./CMS-MERCHANT-ACTIVATION-V1.md)). The setup
wizard automates everything *inside* the CMS; this runbook is everything
*around* it — server, credentials, domain, and the acceptance walk.

Treat the first deployment as a **production acceptance test**, not just
onboarding: the output is evidence (§9), not code.

One merchant = one CMS installation = one `sk_live_` (frozen decision D1).

---

## 1. Prerequisites

- A server (or hosting) with **PHP 8.3+, Composer 2, MySQL/MariaDB** and a web
  server able to serve a Laravel `public/` docroot.
- A domain for the storefront, with the ability to set DNS.
- The merchant exists as a **shop on the DOEH platform** (products/menu priced
  in the shop's currency).
- A merchant **`sk_live_`** issued through the DOEH developer portal
  (`api-client:mint` on the platform side). Keys are issued there and handed to
  the operator out-of-band — the CMS never mints and this runbook never
  transcribes a key.
- Optional (customer sign-in): a DOEH Identity `client_id` + `pk_live_`. This
  is skippable — commerce works without it — and is gated on the identity
  platform's own production activation.

## 2. Create the CMS instance

```bash
git clone https://github.com/beyondplusmyanmar-lab/beyondplus-cms.git
cd beyondplus-cms
make install        # composer deps, .env, app key, permissions, migrate --seed
```

If DB credentials/host differ from the defaults, edit `.env` **before**
`make install` (the Makefile reads `.env` only). The manual equivalent is in
the repo [README](../README.md): `composer install` → `cp .env.example .env` →
set DB → `php artisan migrate --seed`.

Set `APP_URL` to the real storefront domain, `APP_ENV=production`,
`APP_DEBUG=false`.

## 3. Create the admin account and log in

Log into `/bp-admin` with the seeded administrator and change the password
immediately. This admin is the **merchant's** account — the goal of the whole
exercise is that they operate it without developer help.

## 4. Run the DOEH Setup wizard

**Admin → DOEH Setup** (`/bp-admin/doeh-setup`). Six re-entrant steps — each
step's done-ness is computed from live config, so partial runs are safe and any
step can be revisited:

1. **Plugins** — activates DOEH Identity, Commerce and Commerce Storefront in
   dependency order.
2. **Theme** — pick the vertical theme (business / restaurant / retail /
   service). The theme *is* the business model: fulfilment types are declared
   by the theme's manifest and displayed, not chosen (frozen decision D2).
3. **Branding** — logo, favicon, brand settings (same storage as Theme
   Customize).
4. **Commerce key** — paste the `sk_live_`. The wizard **proves it live**
   against the Orders API before saving; a key that fails is never stored, and
   a saved key is never echoed back.
5. **Customer sign-in** *(optional)* — Identity `client_id` + `pk_`; skip
   unless the identity plane is activated for this merchant.
6. **Done** — checklist with links to the storefront, Orders dashboard and
   Customize.

## 5. Validate the Orders connection

Step 4's live proof already validated the key. Confirm from the merchant's
seat: **Admin → Commerce → Orders** loads (an empty list is a pass — the
bounded-window report answered).

## 6. Configure content

Theme Customize page: section toggles, hero, and the vertical's content
(menu sections / product showcase / service catalog per the theme's settings).
The storefront must read as *the merchant's* site before publishing.

## 7. Publish the domain

- Point DNS at the instance; serve HTTPS.
- Behind a proxy/CDN, set the trusted-proxy configuration in `.env` (see
  README) so client IPs and scheme survive.
- Verify the public URL cold: storefront renders, checkout reaches the Orders
  API, no mixed-content.

## 8. First order — the acceptance walk

With the merchant watching (ideally driving):

1. A customer (real phone, public network — not the dev machine) opens the
   storefront, builds an order, picks a fulfilment type offered by the theme,
   and places it.
2. The order returns a confirmation on the storefront.
3. The merchant opens **Admin → Commerce → Orders**, finds the order in
   today's list, and opens the detail (items, amount, customer reference,
   fulfilment, created time).
4. The merchant states, unprompted, what they would do next with the order.

## 9. Evidence to record (the actual deliverable)

| Question | Evidence |
|---|---|
| Can the merchant operate without developer help? | Steps 4–8 driven by the merchant after a single walkthrough |
| Is the theme understandable? | Merchant completed branding/content edits alone |
| Does the order flow match their reality? | Their answer at step 8.4 |
| What's missing? | Verbatim asks (catalog? delivery? payments? notifications?) — each becomes a candidate contract, none pre-built |

The "missing features" list is the **only** input that opens Commerce v2
work — a feature is built when a real merchant hits its absence, not before.

## Failure / rollback notes

- The wizard is re-entrant; re-run any step. A failed key proof stores
  nothing.
- A broken instance can be discarded and rebuilt from §2 — nothing
  merchant-specific lives outside the DB, `.env` and uploaded media.
- Revoking the `sk_live_` at the platform kills the storefront's ordering
  ability immediately; the CMS itself stays up (browsing continues).
