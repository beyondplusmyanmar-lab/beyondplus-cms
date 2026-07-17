# DOEH Commerce

A **server-side connector** that lets a theme submit and read orders through the
DOEH Orders API. It is deliberately small: it owns API auth, request signing,
payload mapping, error normalization, and order retrieval — and nothing else.

**It renders no UI.** No cart, no product catalog, no checkout page, no order
history screen. Those belong to the theme. This plugin is the pipe between the
theme and DOEH; the theme decides what the customer sees.

## The one rule: Model A

The **merchant secret key stays on the server.** It lives only in this plugin's
settings and is never sent to the browser. And a **customer's sign-in is never
the order credential** — the connector authenticates as the *merchant*, using the
secret key, regardless of who is shopping. (Customer identity is a separate
concern; see the DOEH Identity plugin.) There is no method here that accepts a
customer token, by design.

## Setup

1. Get a **merchant secret key** (`sk_test_…` / `sk_live_…`) for this shop, with
   the `orders` scope, from DOEH.
2. Activate the plugin and fill in **Plugins → DOEH Commerce → Settings**:
   Environment (must match the key's `sk_test_`/`sk_live_`), Secret Key, and an
   optional default fulfilment.

## Using it from a theme (server-side)

```php
$result = doeh_commerce()?->createOrder([
    'lines'       => [
        ['sku' => 'COFFEE-250', 'qty' => 2],
        ['sku' => 'MUG-01',     'qty' => 1, 'modifier_ids' => ['engrave']],
    ],
    'customer'    => ['phone' => '+95912345678'], // optional
    'fulfillment' => ['type' => 'pickup'],         // pickup | delivery | dine_in
]);

if ($result && $result['ok']) {
    $order = $result['order'];        // resolved lines + server-computed totals
} else {
    $code = $result['code'] ?? 'EDGE_TRANSPORT'; // stable, e.g. EDGE_UNKNOWN_SKU
    // show your own copy for $code
}
```

You send **what and how many only**. The server resolves SKUs, prices, tax,
discounts and totals — never send a price, currency or grand total.

### Or through hooks (no class dependency)

```php
$result = bp_apply_filters('doeh_create_order', null, $submission);
$order  = bp_apply_filters('doeh_get_order',   null, $orderId);
$report = bp_apply_filters('doeh_list_orders', null, ['from' => $from, 'to' => $to]);
```

Each returns the connector's normalized array, or the default (`null`) when the
plugin is inactive — so a theme degrades gracefully when DOEH Commerce is off.

## The three operations

| Method | Endpoint | Notes |
|---|---|---|
| `createOrder($submission, $idempotencyKey = null)` | `POST /v1/orders` | Auto-generates an Idempotency-Key; pass your own (derived from the cart) to dedupe a real basket across retries. Returns `idempotent: true` on a replay. |
| `getOrder($id)` | `GET /v1/orders/{id}` | Resolved lines + totals. |
| `listOrders($query)` | `GET /v1/orders` | A **bounded time-window report**: `from` and `to` (RFC-3339) are required; the API refuses rather than truncates an over-large result. Optional `limit`, `branch_id`, `status`. |

## Return shape

```
success: ['ok' => true,  'status' => 2xx, 'order' => [...], 'idempotent' => bool]
list:    ['ok' => true,  'status' => 200, 'orders' => [...]]
failure: ['ok' => false, 'status' => int, 'code' => 'EDGE_…'|'API_KEY_…', 'step' => ?string]
```

Never parse human text — branch on `ok`, and on the stable UPPER_SNAKE `code`
(e.g. `EDGE_UNKNOWN_SKU`, `EDGE_INSUFFICIENT_STOCK`, `API_KEY_INVALID`).

## Not in v1

Catalog/product endpoints and webhooks are out of scope for this release. The
connector covers order submission, retrieval, and the windowed report only.
