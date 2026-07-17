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
            'lines'             => $lines,
            'ready'             => function_exists('doeh_commerce') && doeh_commerce() !== null,
            // The selector renders only when there is an actual CHOICE (≥2 types);
            // a pickup-only store keeps today's cart untouched.
            'fulfillment_types' => doeh_storefront_fulfillment_types(),
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

        // Fulfilment is a customer PREFERENCE, forwarded verbatim — never a fee,
        // route or ETA computation. Only a type this storefront actually offers is
        // accepted (a forged value is rejected, not silently coerced to pickup);
        // when the theme collects nothing the field is omitted and the Orders API
        // applies its own default (pickup).
        $fulfillment = trim((string) $request->input('fulfillment'));
        if ($fulfillment !== '') {
            if (! in_array($fulfillment, doeh_storefront_fulfillment_types(), true)) {
                return redirect('/store/cart')->withErrors(
                    doeh_storefront_message('EDGE_INVALID_FULFILLMENT')
                );
            }
            $submission['fulfillment'] = ['type' => $fulfillment];
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
            if ($fulfillment !== '') {
                // The API does not echo fulfilment back yet, so the confirmation
                // shows what THIS session chose — stored only when actually chosen.
                $placed[$id]['fulfillment'] = $fulfillment;
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
                'ok'          => false,
                'order'       => null,
                // Neutral, existence-agnostic copy — never "not found".
                'error'       => 'Order received. Open the confirmation from your checkout to see the details.',
                'fulfillment' => null,
            ]);
        }

        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;
        $result = $connector ? $connector->getOrder($id) : ['ok' => false, 'code' => 'EDGE_TRANSPORT'];

        return doeh_commerce_view('order', [
            'ok'          => $result['ok'] ?? false,
            'order'       => $result['order'] ?? null,
            'error'       => $result['ok'] ?? false ? null : doeh_storefront_message($result['code'] ?? 'EDGE_TRANSPORT'),
            'fulfillment' => $entry['fulfillment'] ?? null,
        ]);
    })->where('id', '[A-Za-z0-9_]+')->name('doeh-storefront.order');
});

// ── Merchant operations: the admin Orders dashboard (Activation v1, Phase 1) ──
// Read-only consumption of the EXISTING Orders API through the connector —
// list (bounded window), search by id, detail. No new platform surface.
Route::middleware('admins')->prefix('bp-admin')->group(function () {

    Route::get('doeh-orders', function (Request $request) {
        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;

        // Search-by-id short-circuits to the detail page. Validate here so a
        // malformed id gets a message, not a 404 from the route constraint.
        $q = trim((string) $request->query('q'));
        if ($q !== '') {
            if (! preg_match('/^[A-Za-z0-9_]+$/', $q)) {
                return redirect(url('bp-admin/doeh-orders'))
                    ->withErrors('Not a valid order id — ids look like ord_1234.');
            }

            return redirect(url('bp-admin/doeh-orders/view/'.$q));
        }

        // The API requires a BOUNDED window. Two date inputs become the
        // half-open range [from 00:00Z, to+1day 00:00Z) so the "to" date is
        // fully included; default = the last 7 days.
        $dateOk = fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
        $fromDate = $request->query('from');
        $toDate   = $request->query('to');
        $fromDate = $dateOk($fromDate) ? $fromDate : now('UTC')->subDays(7)->format('Y-m-d');
        $toDate   = $dateOk($toDate) ? $toDate : now('UTC')->format('Y-m-d');

        $limit = max(1, min(200, (int) $request->query('limit', 50)));
        $status = trim((string) $request->query('status'));
        $branch = (int) $request->query('branch_id');

        $query = [
            'from'  => $fromDate.'T00:00:00Z',
            'to'    => gmdate('Y-m-d', strtotime($toDate.' +1 day UTC')).'T00:00:00Z',
            'limit' => $limit,
        ];
        if ($status !== '') {
            $query['status'] = $status;
        }
        if ($branch > 0) {
            $query['branch_id'] = $branch;
        }

        return view('doeh-commerce-storefront::admin.orders', [
            'configured' => $connector !== null,
            'result'     => $connector?->listOrders($query),
            'from'       => $fromDate,
            'to'         => $toDate,
            'limit'      => $limit,
            'status'     => $status,
            'branch'     => $branch > 0 ? $branch : '',
        ]);
    });

    Route::get('doeh-orders/view/{id}', function (string $id) {
        $connector = function_exists('doeh_commerce') ? doeh_commerce() : null;

        return view('doeh-commerce-storefront::admin.order', [
            'configured' => $connector !== null,
            'result'     => $connector?->getOrder($id),
            'id'         => $id,
        ]);
    })->where('id', '[A-Za-z0-9_]+');
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
            'EDGE_INVALID_FULFILLMENT'       => 'That fulfilment choice is not offered by this store.',
            'EDGE_EMPTY_ORDER'               => 'Your cart is empty.',
            'EDGE_ORDER_NOT_FOUND'           => 'That order could not be found.',
            'EDGE_RESULT_TOO_LARGE'          => 'Too many orders for one page — narrow the date window or raise the limit.',
            'API_KEY_INVALID'                => 'The store is not connected to DOEH correctly.',
            'API_KEY_ENV_MISMATCH'           => 'The store’s DOEH key is for the wrong environment.',
        ][$code] ?? 'Sorry — something went wrong placing your order. Please try again.';
    }
}
