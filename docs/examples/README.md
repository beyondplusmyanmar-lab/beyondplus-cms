# DOEH Bridge — Examples

Three minimal, working starting points for building on the DOEH bridge. Each one
is deliberately small and comment-dense — copy it, rename it, grow it. The
authoritative surface they ride is
[DOEH-BRIDGE-EXTENSION-CONTRACT.md](../DOEH-BRIDGE-EXTENSION-CONTRACT.md); the
theme how-to is [THEME-DESIGN-GUIDE.md](../THEME-DESIGN-GUIDE.md).

| Example | Teaches | Install |
|---|---|---|
| [`example-loyalty-widget/`](./example-loyalty-widget/) | A **plugin** extending identity UI browser-side: `window.DoehIdentity`, the `doeh:identity` event, a theme-consumable filter, the `theme_footer` injection pattern, P1 (PHP never touches a token) | copy to `plugins/example-loyalty-widget/`, activate |
| [`example-theme/`](./example-theme/) | A complete minimal **storefront theme**: required files, settings schema (text/color/image/checkbox), logo + favicon wiring, identity account slot, commerce view overrides, the fulfilment selector, currency-aware money | copy to `resources/views/theme/example-theme/`, activate |
| [`example-commerce-extension/`](./example-commerce-extension/) | A **plugin** consuming the Orders API server-side through hooks (`doeh_list_orders`) — Model A, bounded windows, stable-code branching, an admin page | copy to `plugins/example-commerce-extension/`, activate |

Ground rules every example follows (and yours must too):

1. **P1** — no PHP reads, stores, logs or forwards a customer token. Browser API only.
2. **Model A** — order calls authenticate as the merchant via the connector; a
   customer's sign-in is never the order credential.
3. **Server owns money** — send `{sku, qty}`, format minor units currency-aware,
   never compute or trust a client price.
4. **Branch on stable codes** (`EDGE_…`, `API_KEY_…`), never on human text.
5. **Declare dependencies** in the manifest so activation ordering is enforced.

The example theme is English-only to stay readable; a real theme should meet the
full quality floor in the design guide (bilingual EN/MY, focus states, reduced
motion, mobile).

> ⚠ **The activation security scan rejects a backtick anywhere in a file — even
> inside a comment** (it reads as shell execution). Keep backticks out of your
> plugin/theme sources entirely; these examples are backtick-free for exactly
> that reason.

All three examples were installed, activated (scan-clean) and walked against the
live DOEH sandbox before shipping: the theme served a full guest checkout with
the fulfilment selector and a real order; the widget's filter + footer script
rendered; the takings page summarized the day's orders through the hook.
