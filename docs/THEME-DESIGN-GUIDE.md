# DOEH Theme Design Guide

How to build a theme that wires the DOEH bridge — the layouts you must provide,
the override points, and the identity/commerce integration rules. This is the
"how to build one" companion to
[DOEH-BRIDGE-EXTENSION-CONTRACT.md](./DOEH-BRIDGE-EXTENSION-CONTRACT.md) (the
"what the surface is").

Four reference themes ship with these rules applied:
- **`doeh-business`** — a neutral single-shop storefront.
- **`doeh-restaurant`** — a menu-first restaurant (leader-dot menu card, check,
  order ticket; the fulfilment-selector reference).
- **`doeh-retail`** — a product-card grid retail store.
- **`doeh-service`** — an order-styled service/appointment business (the
  fulfilment opt-out reference).

Copy from whichever is closer to your vertical.

## 1. What a theme owns (and never does)

**Owns:** layout, header, footer, hero, sections, colour, typography, branding,
mobile, and all commerce presentation.

**Never does:** OAuth, token storage, `sk_` handling, direct Orders API calls.
Those belong to the plugins. If your theme is holding a token or a secret key,
it is doing a plugin's job — stop.

## 2. Required files

```
resources/views/theme/<slug>/
├── theme.json
├── layouts/
│   ├── app.blade.php       # <html>, <head>, design tokens, @yield('content')
│   ├── header.blade.php     # brand, nav, cart link, identity account slot
│   └── footer.blade.php      # footer + bp_do_action('theme_footer') + account script
├── index.blade.php          # home
└── commerce/
    ├── shop.blade.php        # overrides the menu/shop
    ├── cart.blade.php        # overrides the cart
    └── order.blade.php       # overrides the order confirmation
```

`theme.json` must declare its plugin dependencies so activation is enforced:
```json
{
  "requires": ["doeh-identity", "doeh-commerce", "doeh-commerce-storefront"],
  "commerce_views": ["shop", "cart", "order"],
  "fulfillment_types": ["pickup", "dine_in"]
}
```

`fulfillment_types` declares which fulfilment choices your vertical offers
(subset of `pickup` / `delivery` / `dine_in`; see §3). Leave it out for
pickup-only; declare `[]` to opt out entirely.

## 3. Commerce override contract

The storefront plugin owns the routes (`/store/*`); your theme owns the pages by
providing `commerce/{shop,cart,order}.blade.php`. Each receives:

| View | Receives |
|---|---|
| `shop` | `products` (`[{sku,name,price_hint}]`), `cart`, `ready` (bool) |
| `cart` | `lines` (products + `qty`), `ready` (bool), `fulfillment_types` (array) |
| `order` | `ok` (bool), `order` (DOEH order or null), `error` (string or null), `fulfillment` (string or null) |

Add-to-cart / remove / checkout are plain forms posting to the plugin routes —
copy the markup from a reference theme:
```blade
<form method="POST" action="{{ url('/store/cart/add') }}">
    @csrf
    <input type="hidden" name="sku" value="{{ $p['sku'] }}">
    <button type="submit">Add</button>
</form>
```
Products for the home page come from `doeh_storefront_products()`.

### The fulfilment selector (v1.1)

Your manifest's `fulfillment_types` drives a radio group in the **cart** view.
Declare only what your vertical really offers — a restaurant says
`["pickup", "dine_in"]`, a service business says `[]` (a service request is an
appointment discussion, not fulfilment). List `pickup` first: it is the safe
default and gets pre-checked. Do **not** declare `delivery` yet — the Orders API
refuses it until the platform's delivery slice lands; when that day comes,
offering it is a manifest flip, no theme code change.

In `commerce/cart.blade.php`, inside the checkout form, render the choice only
when there is one (≥ 2 types) and own your labels (bilingual, on-brand):

```blade
@if (count($fulfillment_types ?? []) > 1)
    @php
        $ftCopy = [
            'pickup'  => [$mm ? 'လာယူမည်' : 'Pickup',  $mm ? 'ကောင်တာမှာ လာယူပါ' : 'Collect at the counter'],
            'dine_in' => [$mm ? 'ဆိုင်တွင် သုံးဆောင်မည်' : 'Dine in', $mm ? 'စားပွဲသို့ ပို့ပေးပါမည်' : 'We’ll bring it to your table'],
        ];
    @endphp
    @foreach ($fulfillment_types as $t)
        @php [$ftLabel, $ftDesc] = $ftCopy[$t] ?? [ucfirst($t), '']; @endphp
        <label>
            <input type="radio" name="fulfillment" value="{{ $t }}" @checked($loop->first)>
            <strong>{{ $ftLabel }}</strong> — {{ $ftDesc }}
        </label>
    @endforeach
@endif
```

In `commerce/order.blade.php`, show what the customer chose (only when chosen —
`$fulfillment` is null for a plain checkout, so guard it):

```blade
@if (! empty($fulfillment))
    {{-- e.g. "Fulfilment · Dine in" on the ticket --}}
@endif
```

Rules:
- Submit the values from `$fulfillment_types` **verbatim** — the plugin rejects
  anything else (`EDGE_INVALID_FULFILLMENT`), it never coerces.
- It is a preference, nothing more: no fee, ETA or logistics UI in the theme.
- A theme that skips the block entirely still works — nothing is submitted and
  the API defaults to pickup.

Copy the working markup from `doeh-restaurant` (`commerce/cart.blade.php` — the
bordered radio rows on the check; `commerce/order.blade.php` — the fulfilment
line on the ticket).

### Money is in minor units — format it currency-aware

`$order['totals']` and each line are in **minor units**. **MMK (and other
zero-decimal currencies) are stored as whole units** — dividing by 100 shows
1,500 MMK as "15". Format like this:

```blade
@php
    $currency = $order['totals']['currency'] ?? '';
    $zeroDecimal = ['MMK', 'JPY', 'KRW', 'VND', 'IDR', 'LAK', 'KHR'];
    $exp = in_array(strtoupper((string) $currency), $zeroDecimal, true) ? 0 : 2;
    $fmt = fn ($minor) => number_format($exp === 0 ? (int) $minor : $minor / (10 ** $exp), $exp);
@endphp
{{ $fmt($order['totals']['grand_total_minor']) }} {{ $currency }}
```

Never send a price or total to the server — you send `{sku, qty}` only; DOEH is
the authority on money.

## 4. Identity integration rules

The theme owns the account UI; the plugin owns the tokens. Wire it like this:

1. **Header** — a mount point, shown only when identity is on:
   ```blade
   @if (function_exists('doeh_identity_enabled') && doeh_identity_enabled())
       <span id="my-account"></span>
   @endif
   ```
2. **Footer** — call the action so the plugin can inject its script, then drive
   the account slot off `window.DoehIdentity`:
   ```blade
   @php bp_do_action('theme_footer') @endphp   {{-- REQUIRED --}}
   <script>
     document.addEventListener('doeh:identity', draw);   // fires on boot + sign-out
     function draw() {
       var id = window.DoehIdentity; if (!id) return;
       if (!id.isSignedIn()) { /* render a Sign in button → id.signIn() */ return; }
       /* render your account UI; id.getCustomer() → {state, customerId, pointsBalance} */
     }
   </script>
   ```
3. **Loyalty** anywhere via the filter: `{!! bp_apply_filters('doeh_loyalty_panel', '') !!}`.

Rules:
- Never read a token in PHP. Customer state comes from the browser API only.
- `getCustomer()` has **no name/phone/profile** — show points, link out to the
  DOEH-hosted profile for the rest.
- Always degrade: if identity is off, the header should fall back gracefully.

## 5. CSS token pattern

Define your palette and type as CSS variables in `layouts/app.blade.php`, expose
a couple as theme settings, and derive everything from them. Suggested tokens
(rename freely):

```css
:root {
  --brand: #…; --ink: #…; --muted: #…; --line: #…;
  --paper: #…;          /* page background */
  --money: #…;          /* totals / success — keep distinct from --brand */
  --danger: #…;
}
```
Keep money in its own colour so a customer can always find the total. Use
`font-variant-numeric: tabular-nums` on prices so columns align.

## 6. Quality floor (non-negotiable)

- Responsive to mobile (test ~390px wide).
- Visible keyboard focus (`:focus-visible`).
- Respect `prefers-reduced-motion`.
- Bilingual EN/MY via `app()->getLocale() === 'mm'`.
- Pass the CMS security scan (no `eval`, backticks, shell, obfuscation).

## 7. Make it yours

Spend your boldness in one place — a single signature element the theme is
remembered by (the restaurant theme's is the leader-dot menu card) — and keep
everything else quiet. A theme should be able to change its entire look without
touching a line of commerce or identity security. That separation is the point.
