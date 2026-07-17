<?php

/**
 * DOEH Commerce Demo routes — loaded only while the plugin (and its required
 * doeh-commerce connector) are active.
 *
 *   GET  /doeh-demo               the fixture "shop" (product cards + add)
 *   POST /doeh-demo/cart/add      add a SKU to the session cart
 *   POST /doeh-demo/cart/remove   remove a SKU
 *   GET  /doeh-demo/cart          the cart + a Place order button
 *   POST /doeh-demo/checkout      → doeh_commerce()->createOrder() → redirect
 *   GET  /doeh-demo/order/{id}    confirmation, read back via getOrder()
 *
 * The cart is session state (doeh_demo_cart: sku => qty). The order credential is
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
        $key = session('doeh_demo_idem');
        if (! $key) {
            $key = 'demo_'.Str::uuid()->toString();
            session(['doeh_demo_idem' => $key]);
        }

        return $key;
    };

    Route::get('/doeh-demo', function () {
        return response()->view('doeh-commerce-demo::shop', [
            'products' => doeh_demo_products(),
            'cart'     => session('doeh_demo_cart', []),
            'ready'    => function_exists('doeh_commerce') && doeh_commerce() !== null,
        ]);
    })->name('doeh-demo.shop');

    Route::post('/doeh-demo/cart/add', function (Request $request) {
        $sku = trim((string) $request->input('sku'));
        $known = array_column(doeh_demo_products(), 'sku');
        if ($sku !== '' && in_array($sku, $known, true)) {
            $cart = session('doeh_demo_cart', []);
            $cart[$sku] = min(($cart[$sku] ?? 0) + 1, 99);
            session(['doeh_demo_cart' => $cart]);
        }

        return redirect('/doeh-demo/cart');
    })->middleware('throttle:60,1')->name('doeh-demo.cart.add');

    Route::post('/doeh-demo/cart/remove', function (Request $request) {
        $sku = trim((string) $request->input('sku'));
        $cart = session('doeh_demo_cart', []);
        unset($cart[$sku]);
        session(['doeh_demo_cart' => $cart]);

        return redirect('/doeh-demo/cart');
    })->middleware('throttle:60,1')->name('doeh-demo.cart.remove');

    Route::get('/doeh-demo/cart', function () {
        $cart = session('doeh_demo_cart', []);
        $byId = [];
        foreach (doeh_demo_products() as $p) {
            $byId[$p['sku']] = $p;
        }
        $lines = [];
        foreach ($cart as $sku => $qty) {
            if (isset($byId[$sku])) {
                $lines[] = $byId[$sku] + ['qty' => (int) $qty];
            }
        }

        return response()->view('doeh-commerce-demo::cart', [
            'lines' => $lines,
            'ready' => function_exists('doeh_commerce') && doeh_commerce() !== null,
        ]);
    })->name('doeh-demo.cart');

    Route::post('/doeh-demo/checkout', function (Request $request) use ($idemFor) {
        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;
        if (! $connector) {
            // Admin-facing: the connector is off/unconfigured. Never a customer crash.
            return redirect('/doeh-demo/cart')->withErrors('DOEH Commerce is not configured.');
        }

        $cart = session('doeh_demo_cart', []);
        $lines = [];
        foreach ($cart as $sku => $qty) {
            $lines[] = ['sku' => (string) $sku, 'qty' => (int) $qty];
        }
        if (! $lines) {
            return redirect('/doeh-demo/cart')->withErrors('Your cart is empty.');
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
            return redirect('/doeh-demo/cart')->withErrors(
                doeh_demo_message($result['code'] ?? 'EDGE_TRANSPORT')
            );
        }

        // Success: clear the cart AND rotate the idempotency key so the next order
        // is a new order, not a replay of this one.
        session()->forget(['doeh_demo_cart', 'doeh_demo_idem']);
        $id = (string) ($result['order']['id'] ?? '');

        return redirect('/doeh-demo/order/'.$id);
    })->middleware('throttle:20,1')->name('doeh-demo.checkout');

    Route::get('/doeh-demo/order/{id}', function (string $id) {
        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;
        $result = $connector ? $connector->getOrder($id) : ['ok' => false, 'code' => 'EDGE_TRANSPORT'];

        return response()->view('doeh-commerce-demo::order', [
            'ok'    => $result['ok'] ?? false,
            'order' => $result['order'] ?? null,
            'error' => $result['ok'] ?? false ? null : doeh_demo_message($result['code'] ?? 'EDGE_TRANSPORT'),
        ]);
    })->where('id', '[A-Za-z0-9_]+')->name('doeh-demo.order');
});

if (! function_exists('doeh_demo_message')) {
    /** Friendly copy for the connector's stable error codes (theme-replaceable). */
    function doeh_demo_message(string $code): string
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
