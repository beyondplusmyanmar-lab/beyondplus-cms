# CMS Commerce v1 — Freeze

**Status: FROZEN — 2026-07-17.** The DOEH commerce lane of the Beyond Plus CMS
bridge is feature-complete for v1 and closed to new capability. This document is
the contract for what "v1" contains, what it deliberately excludes, and the
conditions under which the freeze reopens.

The freeze is a scope decision, not a code state — the surface below is stable
and proven against the live DOEH sandbox; anything in the excluded list is a
separate future decision with its own API contract, not a backlog item that
grows here quietly.

## The shape that is frozen

```
                         Customer (browser)
                               │  OAuth 2.1 + PKCE (customer identity only)
                               ▼
                    doeh-identity  (customer sign-in, loyalty read)
                               │
              ┌────────────────┴────────────────┐
              ▼                                  ▼
       doeh-business theme            doeh-commerce-storefront
       (brand UI, cart UX)            (routes, cart, checkout flow,
              │                        default templates)
              └────────────────┬────────────────┘
                               ▼
                        doeh-commerce            (Model A connector)
                               │  merchant sk_  (server-side only)
                               ▼
                        DOEH Orders API  →  DOEH POS Core
```

| Layer | Responsibility | Package | Version |
|---|---|---|---|
| Identity | Customer OAuth sign-in, loyalty identity state | `doeh-identity` | 0.2.0 |
| Connector | Server-side Orders API bridge (`sk_`) | `doeh-commerce` | 0.1.0 |
| Storefront flow | Routes, cart, checkout, order pages + default templates | `doeh-commerce-storefront` | 0.1.0 |
| Business theme | Brand UI, layout, customer experience | `doeh-business` (theme) | 0.1.0 |
| Orders API | Pricing + order authority | DOEH platform | (existing) |

CMS: **v2.5.0**.

## Included in v1

**Orders (via `doeh-commerce`, Model A):**
- `createOrder` — `POST /v1/orders` (server-priced from `{sku, qty}`; Idempotency-Key)
- `getOrder` — `GET /v1/orders/{id}`
- `listOrders` — `GET /v1/orders` (bounded `from`/`to` window report)

**Storefront (via `doeh-commerce-storefront`):**
- Product display (fixture, `products_json`)
- Session cart (`/store`, `/store/cart`)
- Checkout flow (`/store/checkout` → `createOrder`)
- Order confirmation (`/store/order/{id}` → `getOrder`)

**Theme override points** (a theme owns presentation by providing these views):
- `theme.<active>.commerce.shop`
- `theme.<active>.commerce.cart`
- `theme.<active>.commerce.order`

**Capability / dependency system:**
- Manifest `capabilities` (provides-tokens) and `requires.plugins` / flat `requires`
- Activation, deactivation and boot enforcement of inter-package dependencies

The full public extension surface is specified in
[DOEH-BRIDGE-EXTENSION-CONTRACT.md](./DOEH-BRIDGE-EXTENSION-CONTRACT.md).

## Explicitly excluded from v1

Each of these is a **separate future decision** requiring its own API contract
before any code. None of them may be added to the frozen packages in place.

- ❌ Catalog API (product/price sync from DOEH)
- ❌ Inventory API (stock reads)
- ❌ Payment processing / payment status
- ❌ Marketplace API
- ❌ Webhooks / order-event push
- ❌ Shipping / fulfilment engine
- ❌ Promotions

## The trust boundary (non-negotiable, survives the freeze)

- **Model A only.** The merchant `sk_` lives server-side in the connector; it is
  never sent to the browser. A **customer's OAuth token is never used as the
  order credential** — customer identity and merchant authorization are distinct
  and must never merge. No code path in `doeh-commerce` accepts a customer token.
- **Identity P1.** The identity plugin's PHP layer never reads, stores, logs or
  forwards a customer access/refresh token; all token handling is browser-side.

Reopening the freeze must not weaken either rule.

## Acceptance (the proof behind the freeze)

Run against the live DOEH sandbox; all green as of 2026-07-17:
- `doeh-pos-web` `deploy/dip/golden-client-cms.php` — identity/OAuth surface (25/25)
- `doeh-pos-web` `deploy/dip/golden-client-commerce.php` — Orders connector (8/8)
- Browser walks (headless, sandbox): identity sign-in + loyalty, storefront
  checkout, and the full business-theme journey (identity + commerce in one
  theme) — all green.

## Return conditions (when the freeze reopens)

Reopen only for:
1. **Security fixes** to the trust boundary or token handling.
2. **Correctness bugs** in the frozen surface.
3. **Prod activation** work (real `sk_`/`pk_`/`client_id`, W5 origin config, E5-C).
4. A **v2 decision** that has its own signed API contract for one of the excluded
   capabilities — built as new packages/versions, not folded into v1 in place.

Routine theme work (new brand templates overriding the commerce views) is **not**
a reopen — it is exactly what the override points exist for.
