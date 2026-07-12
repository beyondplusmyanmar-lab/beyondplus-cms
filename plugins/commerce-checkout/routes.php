<?php

/**
 * Commerce Checkout routes — loaded only while active.
 *  Front ('web'):   /cart (view + place order), /cart/add, /cart/update,
 *                   /cart/remove, /checkout, /cart/thank-you/{number}
 *  Admin ('admins'):/bp-admin/orders, /bp-admin/orders/{id}, status update
 *
 * Security: totals are always recomputed server-side from commerce_products
 * (client prices are ignored), the order form has a honeypot, and mutating
 * front routes are throttled. No payment data is handled.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

$cartEnabled = fn () => bp_plugin_option('commerce-checkout', 'checkout_enabled', 'yes') === 'yes';

// Build cart line items from the session, priced from the DB (never the client).
$buildCart = function () {
    $cart = session('commerce_cart', []);
    $items = collect();
    $subtotal = 0.0;
    $count = 0;
    if ($cart && Schema::hasTable('commerce_products')) {
        $products = DB::table('commerce_products')->whereIn('id', array_keys($cart))
            ->where('is_active', 1)->get()->keyBy('id');
        foreach ($cart as $id => $qty) {
            if (! isset($products[$id])) {
                continue;
            }
            $p = $products[$id];
            $qty = max(1, min((int) $qty, 99));
            $line = (float) $p->price * $qty;
            $subtotal += $line;
            $count += $qty;
            $items->push((object) ['id' => (int) $id, 'name' => $p->name, 'price' => (float) $p->price, 'qty' => $qty, 'line' => $line, 'image' => $p->image]);
        }
    }
    return [$items, $subtotal, $count];
};

/* ---------------- Front ---------------- */
Route::middleware('web')->group(function () use ($cartEnabled, $buildCart) {

    Route::post('cart/add', function (Request $request) use ($cartEnabled) {
        abort_unless($cartEnabled(), 404);
        $id = (int) $request->input('product_id');
        if (! Schema::hasTable('commerce_products')
            || ! DB::table('commerce_products')->where('id', $id)->where('is_active', 1)->exists()) {
            return redirect(url('/cart'))->withErrors('That product is unavailable.');
        }
        $cart = session('commerce_cart', []);
        $cart[$id] = min(($cart[$id] ?? 0) + 1, 99);
        session(['commerce_cart' => $cart]);
        return redirect(url('/cart'))->with('success', 'Added to your cart.');
    })->middleware('throttle:30,1');

    Route::get('cart', function () use ($cartEnabled, $buildCart) {
        abort_unless($cartEnabled(), 404);
        [$items, $subtotal, $count] = $buildCart();
        return view('commerce-checkout::cart', [
            'items' => $items, 'subtotal' => $subtotal, 'count' => $count,
            'currency' => bp_plugin_option('commerce', 'currency', 'MMK'),
            'themeSlug' => bp_option('theme', 'default'),
        ]);
    });

    Route::post('cart/update', function (Request $request) use ($cartEnabled) {
        abort_unless($cartEnabled(), 404);
        $qty = (array) $request->input('qty', []);
        $cart = [];
        foreach ($qty as $id => $n) {
            $n = (int) $n;
            if ($n > 0) {
                $cart[(int) $id] = min($n, 99);
            }
        }
        session(['commerce_cart' => $cart]);
        return redirect(url('/cart'))->with('success', 'Cart updated.');
    })->middleware('throttle:30,1');

    Route::post('cart/remove', function (Request $request) use ($cartEnabled) {
        abort_unless($cartEnabled(), 404);
        $cart = session('commerce_cart', []);
        unset($cart[(int) $request->input('product_id')]);
        session(['commerce_cart' => $cart]);
        return redirect(url('/cart'))->with('success', 'Item removed.');
    })->middleware('throttle:30,1');

    Route::post('checkout', function (Request $request) use ($cartEnabled, $buildCart) {
        abort_unless($cartEnabled(), 404);

        // Honeypot: a filled hidden field means a bot — drop it silently.
        if (trim((string) $request->input('website')) !== '') {
            return redirect(url('/cart'));
        }

        $data = $request->validate([
            'customer_name'  => 'required|string|max:120',
            'customer_phone' => 'required|string|max:40',
            'customer_email' => 'nullable|email|max:120',
            'address'        => 'required|string|max:500',
            'note'           => 'nullable|string|max:1000',
        ]);

        [$items, $subtotal, $count] = $buildCart();
        if ($items->isEmpty()) {
            return redirect(url('/cart'))->withErrors('Your cart is empty.');
        }

        $prefix = bp_plugin_option('commerce-checkout', 'order_prefix', 'ORD') ?: 'ORD';
        $number = $prefix.'-'.now()->format('ymd').'-'.strtoupper(Str::random(4));

        $orderId = DB::table('commerce_orders')->insertGetId([
            'order_number'   => $number,
            'customer_name'  => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'] ?? null,
            'address'        => $data['address'],
            'note'           => $data['note'] ?? null,
            'status'         => 'new',
            'subtotal'       => $subtotal,
            'item_count'     => $count,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        foreach ($items as $it) {
            DB::table('commerce_order_items')->insert([
                'order_id'   => $orderId,
                'product_id' => $it->id,
                'name'       => $it->name,
                'price'      => $it->price,
                'qty'        => $it->qty,
                'line_total' => $it->line,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        session()->forget('commerce_cart');
        return redirect(url('/cart/thank-you/'.$number));
    })->middleware('throttle:10,1');

    Route::get('cart/thank-you/{number}', function ($number) use ($cartEnabled) {
        abort_unless($cartEnabled(), 404);
        $order = DB::table('commerce_orders')->where('order_number', $number)->first();
        $items = $order ? DB::table('commerce_order_items')->where('order_id', $order->id)->get() : collect();
        return view('commerce-checkout::thankyou', [
            'order' => $order, 'items' => $items,
            'message' => bp_plugin_option('commerce-checkout', 'thankyou_message', 'Thank you for your order.'),
            'currency' => bp_plugin_option('commerce', 'currency', 'MMK'),
            'themeSlug' => bp_option('theme', 'default'),
        ]);
    });
});

/* ---------------- Admin: orders ---------------- */
Route::middleware('admins')->prefix('bp-admin')->group(function () {

    Route::get('orders', function () {
        $orders = Schema::hasTable('commerce_orders')
            ? DB::table('commerce_orders')->orderByDesc('id')->paginate(30)
            : collect();
        return view('commerce-checkout::admin.index', [
            'orders' => $orders,
            'currency' => bp_plugin_option('commerce', 'currency', 'MMK'),
        ]);
    });

    Route::get('orders/{id}', function ($id) {
        $order = DB::table('commerce_orders')->find($id);
        abort_unless($order, 404);
        $items = DB::table('commerce_order_items')->where('order_id', $id)->get();
        return view('commerce-checkout::admin.show', [
            'order' => $order, 'items' => $items,
            'currency' => bp_plugin_option('commerce', 'currency', 'MMK'),
        ]);
    })->whereNumber('id');

    Route::post('orders/{id}/status', function (Request $request, $id) {
        $data = $request->validate(['status' => 'required|in:new,confirmed,completed,cancelled']);
        DB::table('commerce_orders')->where('id', $id)->update(['status' => $data['status'], 'updated_at' => now()]);
        return redirect(url('bp-admin/orders/'.$id))->with('success', 'Order status updated.');
    })->whereNumber('id');
});
