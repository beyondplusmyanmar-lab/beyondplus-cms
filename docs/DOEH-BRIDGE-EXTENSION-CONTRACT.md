# DOEH Bridge — Extension Contract

The public, stable surface a **theme** or **plugin** may build on to integrate a
Beyond Plus CMS site with the DOEH platform. Everything here is contract: names,
signatures, return shapes and events will not change without a version bump.
Anything **not** listed is internal and may change.

Companion to the CMS's generic [plugin-development.md](./plugin-development.md)
and [theme-development.md](./theme-development.md); this document covers only the
DOEH-specific packages.

## Packages and the trust model

| Package | Kind | Role |
|---|---|---|
| `doeh-identity` | plugin | Browser OAuth client — customer sign-in + loyalty read |
| `doeh-commerce` | plugin | Server-side Orders API connector (Model A) |
| `doeh-commerce-storefront` | plugin | Storefront flow: routes, cart, checkout + default templates |
| `doeh-business` | theme | Reference storefront wiring both plugins |

Two rules bound every integration and must never be crossed:

- **Identity is browser-side (P1).** Customer access/refresh tokens live only in
  the browser. No PHP — yours or the plugin's — reads, stores, logs or forwards
  one. Themes drive identity through `window.DoehIdentity`, never a server token.
- **Commerce is Model A.** The merchant `sk_` is server-side only; a customer's
  OAuth token is never the order credential. Order calls authenticate as the
  *merchant*, regardless of who is shopping.

---

## 1. Capability & dependency system

Plugins and themes declare what they provide and what they need; the loader
enforces it.

**Manifest keys** (`plugin.json` / `theme.json`):
```json
{
  "capabilities": ["orders.create", "orders.read"],
  "requires": { "php": "8.1", "plugins": ["doeh-commerce"] }
}
```
- `capabilities` — opaque provides-tokens other packages can require.
- `requires.plugins` — plugin ids that must be active. A **theme** may instead
  use a flat `"requires": ["doeh-identity", "doeh-commerce"]`.

**Declared capabilities:**
| Package | Provides |
|---|---|
| `doeh-identity` | `identity.signin`, `loyalty.read` |
| `doeh-commerce` | `orders.create`, `orders.read` |
| `doeh-commerce-storefront` | `commerce.storefront` |

**Enforcement** (`App\Support\Plugin`):
- Activation is **blocked** if a required plugin is not active.
- Deactivation is **refused** while an active package depends on it.
- Boot **skips** a package whose dependencies are unmet.

**Discovery API:** `Plugin::capabilitiesOf($slug)`, `requiredPlugins($slug,$meta=null)`,
`missingDependencies($slug,$meta=null)`, `dependents($slug)`, `capabilityRegistry()`.

---

## 2. Identity plugin (`doeh-identity`)

### Browser API — `window.DoehIdentity`
Available on every page while the plugin is enabled. The theme's own account UI
is built on top of this; it never sees a token.

| Method | Returns | Purpose |
|---|---|---|
| `signIn()` | — | Start the hosted-login redirect |
| `signOut()` | Promise | Revoke the session, re-render widgets |
| `isSignedIn()` | boolean | Session exists in this tab |
| `getCustomer()` | Promise | `{ state, customerId, pointsBalance }` (cached; `getCustomer(true)` refetches) |
| `getCustomerToken()` | Promise | Access token (or `null`) — for DOEH consumer API calls **only** |
| `render()` | — | Re-render all mount points |

`getCustomer()` carries loyalty standing only — **no name / phone / profile**
(profile is a sensitive scope reserved for native apps). Link to the DOEH-hosted
profile page instead of rendering one.

### Event
```js
document.addEventListener('doeh:identity', function (e) {
  // e.detail.signedIn — fired on boot and on sign-out
});
```

### Mount points (three equivalent ways)
- Shortcodes in content: `[doeh_signin]`, `[doeh_loyalty]`
- Markup: `<div data-doeh-widget="signin"></div>`, `<div data-doeh-widget="loyalty"></div>`
- Theme filters: `{!! bp_apply_filters('doeh_signin_button', '') !!}`,
  `{!! bp_apply_filters('doeh_loyalty_panel', '') !!}`

### PHP helpers
- `doeh_identity_enabled(): bool` — on and configured
- `doeh_identity_config(): array` — public config (safe to print; no secrets)

The plugin injects its config + `widget.js` on the `theme_footer` action, so a
theme's footer **must** call `bp_do_action('theme_footer')`.

---

## 3. Commerce connector (`doeh-commerce`)

Server-side only. The theme/flow calls it; it authenticates as the merchant.

### PHP helper
```php
$client = doeh_commerce();            // DoehCommerceClient, or null if off/unconfigured
```

| Method | Endpoint | Notes |
|---|---|---|
| `createOrder($submission, $idempotencyKey = null)` | `POST /v1/orders` | Server-priced from `{sku, qty}`; pass a cart-derived key to dedupe retries |
| `getOrder($id)` | `GET /v1/orders/{id}` | Resolved lines + totals |
| `listOrders($query)` | `GET /v1/orders` | Bounded report — `from`+`to` (RFC-3339) **required** |

### Submission shape (`createOrder`)
```php
$submission = [
    'lines'       => [['sku' => 'COFFEE-250', 'qty' => 2]],   // required
    'customer'    => ['phone' => '+95912345678'],             // optional
    'fulfillment' => ['type' => 'pickup'],                    // optional: pickup | delivery | dine_in
];
```
`fulfillment.type` is a customer **preference**, forwarded as-is; the Orders API
decides which types it accepts (an unaccepted type fails with
`EDGE_FULFILLMENT_NOT_AVAILABLE` — `delivery` does today, until the platform's
delivery slice lands). A value outside the three known types is dropped by the
connector, never sent.

### Hooks (no class dependency)
```php
$r = bp_apply_filters('doeh_create_order', null, $submission, $idempotencyKey);
$r = bp_apply_filters('doeh_get_order',    null, $orderId);
$r = bp_apply_filters('doeh_list_orders',  null, ['from' => $from, 'to' => $to]);
```
Each returns the normalized array, or the default (`null`) when the plugin is off
— so a theme degrades gracefully.

### Normalized return shape
```php
// success
['ok' => true,  'status' => 2xx, 'order' => [...], 'idempotent' => bool]
// list
['ok' => true,  'status' => 200, 'orders' => [...]]
// failure
['ok' => false, 'status' => int, 'code' => 'EDGE_…'|'API_KEY_…', 'step' => ?string]
```

**Never parse human text.** Branch on `ok`, then on the stable UPPER_SNAKE `code`:

| Code | Meaning |
|---|---|
| `EDGE_UNKNOWN_SKU` | SKU not in the shop's catalog |
| `EDGE_UNPRICED_SKU` | SKU has no price |
| `EDGE_INSUFFICIENT_STOCK` | Not enough stock |
| `EDGE_FULFILLMENT_NOT_AVAILABLE` | Fulfilment type not accepted by the Orders API (today: `delivery`) |
| `EDGE_EMPTY_ORDER` | No lines |
| `EDGE_ORDER_NOT_FOUND` | Unknown order id |
| `EDGE_BAD_BODY` | Malformed request (incl. missing `from`/`to`) |
| `API_KEY_INVALID` / `API_KEY_ENV_MISMATCH` | Key misconfigured (admin-facing) |
| `EDGE_TRANSPORT` | Network/transport failure (client-injected) |

You send **what and how many only** — never a price, currency or total. The
server is the sole authority on money.

---

## 4. Storefront flow (`doeh-commerce-storefront`)

Owns the commerce routes and default templates. A theme **overrides the
templates**; it does not re-implement the routes (a theme cannot own routes in
this CMS).

### Public routes
| Method | Path | Renders |
|---|---|---|
| GET | `/store` | shop |
| POST | `/store/cart/add` | (redirect to cart) |
| POST | `/store/cart/remove` | (redirect to cart) |
| GET | `/store/cart` | cart |
| POST | `/store/checkout` | → `createOrder` → redirect to order |
| GET | `/store/order/{id}` | order confirmation |

Checkout mints a cart-scoped `Idempotency-Key` on first fill and rotates it after
a successful order, so a retried submit replays one order rather than duplicating.

Checkout accepts an optional `fulfillment` form field (see *Fulfilment
preference* below). A submitted value the store does not offer is rejected back
to the cart with `EDGE_INVALID_FULFILLMENT` — never silently coerced; an omitted
field is simply not sent, and the Orders API applies its own default (pickup).

### Fulfilment preference (v1.1, storefront ≥ 0.2.0)

The active **theme's manifest** declares which fulfilment types the storefront
offers:

```json
{ "fulfillment_types": ["pickup", "dine_in"] }
```

- Allowed values: `pickup`, `delivery`, `dine_in` (unknown entries are ignored).
- `[]` opts the theme out entirely (e.g. a service/appointment vertical) — any
  submitted `fulfillment` value is then rejected.
- **Absent** = `["pickup"]`, the pre-v1.1 behaviour: no selector, nothing
  submitted, the API default applies. Existing themes need no change.
- The default cart template renders a radio selector only when **≥ 2** types are
  offered (first entry pre-checked — list `pickup` first).

This is **preference collection only**. The storefront never computes a delivery
fee, rider, route, ETA or logistics status, and the Orders API remains the
authority on which types it accepts — `delivery` is refused server-side
(`EDGE_FULFILLMENT_NOT_AVAILABLE`) until the platform's delivery slice lands, so
no shipped manifest offers it yet; offering it later is a manifest flip.

The chosen type is remembered in the placing session and handed to the `order`
view (the Orders API does not echo fulfilment back yet).

### Theme override contract
When the active theme provides a view, the flow renders it instead of its
default. Provide any of:

| View | Rendered by | Receives |
|---|---|---|
| `theme.<active>.commerce.shop` | `GET /store` | `products` (array of `{sku,name,price_hint}`), `cart`, `ready` (bool) |
| `theme.<active>.commerce.cart` | `GET /store/cart` | `lines` (products + `qty`), `ready` (bool), `fulfillment_types` (array) |
| `theme.<active>.commerce.order` | `GET /store/order/{id}` | `ok` (bool), `order` (DOEH order or null), `error` (string or null), `fulfillment` (string or null — the type this session chose) |

A `cart` view that offers the choice submits it as the `fulfillment` field of the
checkout form, using the values from `fulfillment_types` verbatim. Guard the
selector with `count($fulfillment_types ?? []) > 1` and show `$fulfillment` on
the confirmation only when non-null.

`$order['totals']` are in **minor units** (`grand_total_minor`, `currency`).

### PHP helpers
- `doeh_storefront_products(): array` — the configured fixture (falls back to the
  manifest default)
- `doeh_storefront_fulfillment_types(): array` — the offered fulfilment types
  (the active theme's declaration, validated; `['pickup']` when undeclared)
- `doeh_storefront_fulfillment_label(string $type): array` — `[label, description]`
  display copy used by the **default** templates (a theme owns its own words)
- `doeh_storefront_message(string $code): string` — friendly, replaceable copy for
  a connector error code (includes `EDGE_INVALID_FULFILLMENT`, the storefront's
  own client-side rejection of a type the store does not offer)
- `doeh_commerce_view(string $name, array $data)` — the theme-override resolver
  (used internally by the routes)

The session cart is `doeh_store_cart` (`sku => qty`).

---

## 5. Security rules for extenders

1. **Never put `sk_` in the browser.** It is server-side config; the connector
   sends it, nothing else may.
2. **Never use a customer token as an order credential.** Identity ≠ merchant
   authorization (Model A). If you want an order, call `doeh_commerce()`.
3. **Never store or forward a customer token in PHP** (P1). Read customer state
   in the browser via `window.DoehIdentity`.
4. **Trust the server on money.** Send `{sku, qty}`; never a price or total.
5. **Branch on stable `code`, not HTTP status or human text.** Show your own copy.
6. **Declare dependencies.** If your theme/plugin needs a DOEH package, list it in
   `requires` so activation is ordered and enforced.
7. **An order id is a reference, not a credential.** Never reveal order details
   from an id alone — the `/store/order/{id}` route already binds the
   confirmation to the session that placed it, so your `commerce.order` view
   just renders what it is handed. If you build your own order-lookup surface,
   session- or identity-bind it the same way, return a generic 200 for anything
   the caller did not place, and never 404 (a 404 leaks which ids exist).

Following these keeps a theme free to change entirely without touching commerce
security — which is the whole point of the split.
