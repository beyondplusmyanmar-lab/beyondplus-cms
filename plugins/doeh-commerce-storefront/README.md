# DOEH Commerce Storefront

The **storefront flow** over the DOEH Commerce connector: a product fixture, a
session cart, and the shop → cart → checkout → order routes that call
`doeh_commerce()->createOrder()`. It ships default templates so it works
standalone on any theme, and a theme takes over the presentation by overriding
`theme.<active>.commerce.{shop,cart,order}` — the WooCommerce template-override
model. The plugin owns the flow; the theme owns the look.

It holds **no secret** and speaks to DOEH only through the `doeh-commerce`
connector (which it requires). The cart is plain session state.

## Routes

| Method | Path | Renders |
|---|---|---|
| GET | `/store` | shop (the product fixture) |
| POST | `/store/cart/add` | → cart |
| POST | `/store/cart/remove` | → cart |
| GET | `/store/cart` | cart + checkout form |
| POST | `/store/checkout` | → `createOrder` → order confirmation |
| GET | `/store/order/{id}` | confirmation (session-bound, see below) |

Checkout mints a **cart-scoped Idempotency-Key** on first fill and rotates it
after a successful order — a retried submit replays one order, never two.

## Setup

1. Activate **DOEH Commerce** (the connector) and configure its key first —
   this plugin requires it and ordering is enforced.
2. Fill in **Plugins → DOEH Commerce Storefront → Settings**: the storefront
   products (SKUs must exist in your DOEH catalog; the price hint is display
   only — DOEH computes the real total at checkout).

## Fulfilment preference (v1.1)

The **active theme's manifest** declares which fulfilment choices the checkout
offers:

```json
{ "fulfillment_types": ["pickup", "dine_in"] }
```

- Allowed values: `pickup`, `delivery`, `dine_in` (unknown entries ignored).
- `[]` opts out entirely (service/appointment verticals).
- **Absent** = `["pickup"]` — the pre-v1.1 behaviour: no selector, nothing
  submitted, the Orders API applies its default. Existing themes need no change.
- The cart renders a radio selector only when **≥ 2** types are offered (first
  entry pre-checked — list `pickup` first).

The chosen type is forwarded verbatim as the Orders API's existing
`fulfillment.type` field. A submitted value the store does not offer is rejected
back to the cart (`EDGE_INVALID_FULFILLMENT`) — never coerced. The confirmation
page shows what the placing session chose.

**Preference only.** This plugin computes no delivery fee, rider, route, ETA or
logistics status; the Orders API stays the authority on which types it accepts.
`delivery` is currently refused server-side (`EDGE_FULFILLMENT_NOT_AVAILABLE`)
until the platform's delivery slice lands — don't declare it yet; enabling it
later is a theme-manifest flip, not a code change.

## Admin: the Orders dashboard (v0.3.0, Merchant Activation v1 Phase 1)

**Admin → DOEH Orders** (`/bp-admin/doeh-orders`, `admins` middleware) — the
merchant's read-only operations view over the same connector:

- **List**: the bounded window report (`GET /v1/orders`), defaulting to the last
  7 days, with date/status/branch/limit filters. Report rows carry money as
  `total.amount_minor` with an authoritative `scale` — the page formats with it.
  An over-large result surfaces `EDGE_RESULT_TOO_LARGE` with a "narrow the
  window" hint (the API refuses rather than truncates).
- **Search by id** → the detail page; a malformed id gets a message, not a 404.
- **Detail** (`GET /v1/orders/{id}`): lines, currency-aware totals, status,
  customer contact when present, fulfilment when the API reports it, and a
  collapsible raw payload for debugging.

No new platform surface — it only consumes the two existing read endpoints.

## Theme override contract

| View | Receives |
|---|---|
| `theme.<active>.commerce.shop` | `products` (`[{sku,name,price_hint}]`), `cart`, `ready` |
| `theme.<active>.commerce.cart` | `lines` (products + `qty`), `ready`, `fulfillment_types` |
| `theme.<active>.commerce.order` | `ok`, `order` (or null), `error` (or null), `fulfillment` (or null) |

`$order['totals']` are **minor units** and MMK is zero-decimal — format
currency-aware (see `docs/THEME-DESIGN-GUIDE.md` §3 for the snippet, and for the
fulfilment radio-group + confirmation-guard markup).

## PHP helpers

- `doeh_storefront_products(): array` — the configured fixture (falls back to
  the manifest default).
- `doeh_storefront_fulfillment_types(): array` — the offered fulfilment types
  (the active theme's declaration, validated; `['pickup']` when undeclared).
- `doeh_storefront_fulfillment_label(string $type): array` — `[label, description]`
  copy for the default templates (a theme owns its own words).
- `doeh_storefront_message(string $code): string` — friendly, replaceable copy
  for a stable error code.
- `doeh_commerce_view(string $name, array $data)` — the theme-override resolver.

Session keys: `doeh_store_cart` (`sku => qty`), `doeh_store_idem` (the cart's
Idempotency-Key), `doeh_store_orders` (the confirmation binding).

## The confirmation is session-bound (not an order lookup)

An order id is a **reference, not a credential**. `/store/order/{id}` shows
details only to the session that placed the order (24 h); every other id — a
real order this session did not place, or a nonexistent one — gets the same
generic 200 with no details and never a 404, so the page is not an oracle for
which ids exist. Don't build a page that reveals order contents from an id
alone.

## Not in this plugin

Payment, delivery execution/logistics, catalog sync, inventory, webhooks — see
`docs/CMS-COMMERCE-V1-FREEZE.md` for the frozen scope and the conditions under
which it reopens.
