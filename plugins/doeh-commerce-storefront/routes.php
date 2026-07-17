<?php

/**
 * DOEH Commerce Storefront routes — loaded only while the plugin (and its required
 * doeh-commerce connector) are active.
 *
 *   GET  /store               the fixture "shop" (product cards + add)
 *   POST /store/cart/add      add a SKU to the session cart
 *   POST /store/cart/remove   remove a SKU
 *   GET  /store/cart          the cart + a Place order button
 *   POST /store/checkout      → doeh_commerce()->createOrder() → redirect
 *   GET  /store/order/{id}    confirmation, read back via getOrder()
 *
 * The cart is session state (doeh_store_cart: sku => qty). The order credential is
 * the merchant key inside the connector — never anything from this request.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

Route::middleware('web')->group(function () {

    // Idempotency-Key for the current cart: minted when the cart first fills,
    // stable across retries of the SAME cart, rotated after a successful order.
    // A create that times out and is retried therefore replays one order, never
    // duplicates it — the property a checkout must have.
    $idemFor = function (array $cart): string {
        $key = session('doeh_store_idem');
        if (! $key) {
            $key = 'store_'.Str::uuid()->toString();
            session(['doeh_store_idem' => $key]);
        }

        return $key;
    };

    Route::get('/store', function () {
        return doeh_commerce_view('shop', [
            'products' => doeh_storefront_products(),
            'cart'     => session('doeh_store_cart', []),
            'ready'    => function_exists('doeh_commerce') && doeh_commerce() !== null,
        ]);
    })->name('doeh-storefront.shop');

    Route::post('/store/cart/add', function (Request $request) {
        $sku = trim((string) $request->input('sku'));
        $known = array_column(doeh_storefront_products(), 'sku');
        if ($sku !== '' && in_array($sku, $known, true)) {
            $cart = session('doeh_store_cart', []);
            $cart[$sku] = min(($cart[$sku] ?? 0) + 1, 99);
            session(['doeh_store_cart' => $cart]);
        }

        return redirect('/store/cart');
    })->middleware('throttle:60,1')->name('doeh-storefront.cart.add');

    Route::post('/store/cart/remove', function (Request $request) {
        $sku = trim((string) $request->input('sku'));
        $cart = session('doeh_store_cart', []);
        unset($cart[$sku]);
        session(['doeh_store_cart' => $cart]);

        return redirect('/store/cart');
    })->middleware('throttle:60,1')->name('doeh-storefront.cart.remove');

    Route::get('/store/cart', function () {
        $cart = session('doeh_store_cart', []);
        $byId = [];
        foreach (doeh_storefront_products() as $p) {
            $byId[$p['sku']] = $p;
        }
        $lines = [];
        foreach ($cart as $sku => $qty) {
            if (isset($byId[$sku])) {
                $lines[] = $byId[$sku] + ['qty' => (int) $qty];
            }
        }

        return doeh_commerce_view('cart', [
            'lines' => $lines,
            'ready' => function_exists('doeh_commerce') && doeh_commerce() !== null,
        ]);
    })->name('doeh-storefront.cart');

    Route::post('/store/checkout', function (Request $request) use ($idemFor) {
        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;
        if (! $connector) {
            // Admin-facing: the connector is off/unconfigured. Never a customer crash.
            return redirect('/store/cart')->withErrors('DOEH Commerce is not configured.');
        }

        $cart = session('doeh_store_cart', []);
        $lines = [];
        foreach ($cart as $sku => $qty) {
            $lines[] = ['sku' => (string) $sku, 'qty' => (int) $qty];
        }
        if (! $lines) {
            return redirect('/store/cart')->withErrors('Your cart is empty.');
        }

        $phone = trim((string) $request->input('phone'));
        $submission = ['lines' => $lines];
        if ($phone !== '') {
            $submission['customer'] = ['phone' => $phone];
        }

        $result = $connector->createOrder($submission, $idemFor($cart));

        if (! ($result['ok'] ?? false)) {
            // Map the stable connector code to friendly copy — the theme decides
            // the words; the customer never sees an HTTP status or edge code.
            return redirect('/store/cart')->withErrors(
                doeh_storefront_message($result['code'] ?? 'EDGE_TRANSPORT')
            );
        }

        $id = (string) ($result['order']['id'] ?? '');

        // Object-level authorization for the confirmation page: remember that THIS
        // session placed THIS order, so /store/order/{id} only reveals details to
        // the browser that created it. The order id is a reference, not a
        // credential — without this, its sequential ids are trivially enumerable.
        if ($id !== '') {
            $placed = (array) session('doeh_store_orders', []);
            $placed[$id] = ['created_at' => time()];
            if ($phone !== '') {
                $placed[$id]['phone_hash'] = hash('sha256', $phone); // for a future receipt check
            }
            session(['doeh_store_orders' => $placed]);
        }

        // Clear the cart AND rotate the idempotency key so the next order is a new
        // order, not a replay of this one.
        session()->forget(['doeh_store_cart', 'doeh_store_idem']);

        return redirect('/store/order/'.$id);
    })->middleware('throttle:20,1')->name('doeh-storefront.checkout');

    // How long a guest confirmation stays viewable in the session that placed it.
    $confirmationTtl = 24 * 3600;

    Route::get('/store/order/{id}', function (string $id) use ($confirmationTtl) {
        // Only the session that placed this order may see its details. For every
        // other id — a real order this session did not place, OR a nonexistent one —
        // we return the SAME generic confirmation, so the page is not an oracle for
        // which order ids exist. No merchant API call is made in that case.
        $placed = (array) session('doeh_store_orders', []);
        $entry = $placed[$id] ?? null;
        $owned = is_array($entry) && (time() - (int) ($entry['created_at'] ?? 0) < $confirmationTtl);

        if (! $owned) {
            return doeh_commerce_view('order', [
                'ok'    => false,
                'order' => null,
                // Neutral, existence-agnostic copy — never "not found".
                'error' => 'Order received. Open the confirmation from your checkout to see the details.',
            ]);
        }

        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;
        $result = $connector ? $connector->getOrder($id) : ['ok' => false, 'code' => 'EDGE_TRANSPORT'];

        return doeh_commerce_view('order', [
            'ok'    => $result['ok'] ?? false,
            'order' => $result['order'] ?? null,
            'error' => $result['ok'] ?? false ? null : doeh_storefront_message($result['code'] ?? 'EDGE_TRANSPORT'),
        ]);
    })->where('id', '[A-Za-z0-9_]+')->name('doeh-storefront.order');
});

if (! function_exists('doeh_storefront_message')) {
    /** Friendly copy for the connector's stable error codes (theme-replaceable). */
    function doeh_storefront_message(string $code): string
    {
        return [
            'EDGE_UNKNOWN_SKU'               => 'One of these products is no longer available.',
            'EDGE_UNPRICED_SKU'              => 'One of these products has no price set.',
            'EDGE_INSUFFICIENT_STOCK'        => 'Sorry — not enough stock for your order.',
            'EDGE_FULFILLMENT_NOT_AVAILABLE' => 'That fulfilment option is not available right now.',
            'EDGE_EMPTY_ORDER'               => 'Your cart is empty.',
            'EDGE_ORDER_NOT_FOUND'           => 'That order could not be found.',
            'API_KEY_INVALID'                => 'The store is not connected to DOEH correctly.',
            'API_KEY_ENV_MISMATCH'           => 'The store’s DOEH key is for the wrong environment.',
        ][$code] ?? 'Sorry — something went wrong placing your order. Please try again.';
    }
}
