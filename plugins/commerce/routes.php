<?php

/**
 * Commerce routes — loaded only while the plugin is active.
 *  - /bp-admin/commerce            products
 *  - /bp-admin/commerce/promotions campaigns (date-windowed)
 *  - /bp-admin/commerce/branches   store locations
 *  - /shop                         front catalogue (rendered in the active theme)
 *
 * Uses the query builder (no plugin-autoloaded model classes) and never deletes
 * files, keeping the plugin clear of the activation security scan. Numeric-id
 * routes are constrained with whereNumber so /commerce/promotions and
 * /commerce/branches are not read as a product id.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

// Move an uploaded image into public/uploads and return its filename (or null).
$upload = function (Request $request) {
    if (! $request->hasFile('image')) {
        return null;
    }
    $file = $request->file('image');
    $name = time().'_'.Str::random(6).'.'.strtolower($file->getClientOriginalExtension());
    $file->move(public_path('uploads'), $name);
    return $name;
};

Route::middleware('admins')->prefix('bp-admin')->group(function () use ($upload) {

    /* ---------------- Products ---------------- */
    Route::get('commerce', function () {
        $products = Schema::hasTable('commerce_products')
            ? DB::table('commerce_products')->orderBy('sort_order')->orderByDesc('id')->get()
            : collect();

        return view('commerce::admin.index', [
            'products' => $products,
            'currency' => bp_plugin_option('commerce', 'currency', 'MMK'),
        ]);
    });

    Route::get('commerce/create', fn () => view('commerce::admin.form', ['product' => null]));

    Route::get('commerce/{id}/edit', function ($id) {
        $product = DB::table('commerce_products')->find($id);
        abort_unless($product, 404);
        return view('commerce::admin.form', ['product' => $product]);
    })->whereNumber('id');

    $saveProduct = function (Request $request, $id = null) use ($upload) {
        $data = $request->validate([
            'name'        => 'required|string|max:190',
            'price'       => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
        ]);
        bp_validate_images($request, ['image']);

        $row = [
            'name'        => $data['name'],
            'price'       => $data['price'] ?? 0,
            'description' => $data['description'] ?? null,
            'is_featured' => $request->input('is_featured') === 'yes' ? 1 : 0,
            'is_active'   => $request->input('is_active') === 'yes' ? 1 : 0,
            'sort_order'  => $data['sort_order'] ?? 0,
            'updated_at'  => now(),
        ];
        if ($img = $upload($request)) {
            $row['image'] = $img;
        }

        if ($id) {
            DB::table('commerce_products')->where('id', $id)->update($row);
            $msg = 'Product updated.';
        } else {
            $row['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));
            $row['created_at'] = now();
            DB::table('commerce_products')->insert($row);
            $msg = 'Product created.';
        }
        return redirect(url('bp-admin/commerce'))->with('success', $msg);
    };
    Route::post('commerce', fn (Request $request) => $saveProduct($request));
    Route::post('commerce/{id}', fn (Request $request, $id) => $saveProduct($request, $id))->whereNumber('id');
    Route::get('commerce/{id}/delete', function ($id) {
        DB::table('commerce_products')->where('id', $id)->delete();
        return redirect(url('bp-admin/commerce'))->with('success', 'Product deleted.');
    })->whereNumber('id');

    /* ---------------- Promotions ---------------- */
    Route::get('commerce/promotions', function () {
        $promos = Schema::hasTable('commerce_promotions')
            ? DB::table('commerce_promotions')->orderBy('sort_order')->orderByDesc('id')->get()
            : collect();
        return view('commerce::admin.promotions.index', ['promos' => $promos]);
    });
    Route::get('commerce/promotions/create', fn () => view('commerce::admin.promotions.form', ['promo' => null]));
    Route::get('commerce/promotions/{id}/edit', function ($id) {
        $promo = DB::table('commerce_promotions')->find($id);
        abort_unless($promo, 404);
        return view('commerce::admin.promotions.form', ['promo' => $promo]);
    })->whereNumber('id');

    $savePromo = function (Request $request, $id = null) use ($upload) {
        $data = $request->validate([
            'title'       => 'required|string|max:190',
            'description' => 'nullable|string',
            'link'        => 'nullable|string|max:255',
            'badge'       => 'nullable|string|max:40',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date',
            'sort_order'  => 'nullable|integer',
        ]);
        bp_validate_images($request, ['image']);

        $row = [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'link'        => $data['link'] ?? null,
            'badge'       => $data['badge'] ?? null,
            'starts_at'   => $data['starts_at'] ?? null,
            'ends_at'     => $data['ends_at'] ?? null,
            'is_active'   => $request->input('is_active') === 'yes' ? 1 : 0,
            'sort_order'  => $data['sort_order'] ?? 0,
            'updated_at'  => now(),
        ];
        if ($img = $upload($request)) {
            $row['image'] = $img;
        }

        if ($id) {
            DB::table('commerce_promotions')->where('id', $id)->update($row);
            $msg = 'Promotion updated.';
        } else {
            $row['created_at'] = now();
            DB::table('commerce_promotions')->insert($row);
            $msg = 'Promotion created.';
        }
        return redirect(url('bp-admin/commerce/promotions'))->with('success', $msg);
    };
    Route::post('commerce/promotions', fn (Request $request) => $savePromo($request));
    Route::post('commerce/promotions/{id}', fn (Request $request, $id) => $savePromo($request, $id))->whereNumber('id');
    Route::get('commerce/promotions/{id}/delete', function ($id) {
        DB::table('commerce_promotions')->where('id', $id)->delete();
        return redirect(url('bp-admin/commerce/promotions'))->with('success', 'Promotion deleted.');
    })->whereNumber('id');

    /* ---------------- Branches (store locations) ---------------- */
    Route::get('commerce/branches', function () {
        $branches = Schema::hasTable('commerce_branches')
            ? DB::table('commerce_branches')->orderBy('sort_order')->orderByDesc('id')->get()
            : collect();
        return view('commerce::admin.branches.index', ['branches' => $branches]);
    });
    Route::get('commerce/branches/create', fn () => view('commerce::admin.branches.form', ['branch' => null]));
    Route::get('commerce/branches/{id}/edit', function ($id) {
        $branch = DB::table('commerce_branches')->find($id);
        abort_unless($branch, 404);
        return view('commerce::admin.branches.form', ['branch' => $branch]);
    })->whereNumber('id');

    $saveBranch = function (Request $request, $id = null) {
        $data = $request->validate([
            'name'       => 'required|string|max:190',
            'address'    => 'nullable|string',
            'phone'      => 'nullable|string|max:60',
            'hours'      => 'nullable|string|max:190',
            'map_embed'  => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        $row = [
            'name'       => $data['name'],
            'address'    => $data['address'] ?? null,
            'phone'      => $data['phone'] ?? null,
            'hours'      => $data['hours'] ?? null,
            'map_embed'  => $data['map_embed'] ?? null,
            'is_active'  => $request->input('is_active') === 'yes' ? 1 : 0,
            'sort_order' => $data['sort_order'] ?? 0,
            'updated_at' => now(),
        ];
        if ($id) {
            DB::table('commerce_branches')->where('id', $id)->update($row);
            $msg = 'Location updated.';
        } else {
            $row['created_at'] = now();
            DB::table('commerce_branches')->insert($row);
            $msg = 'Location created.';
        }
        return redirect(url('bp-admin/commerce/branches'))->with('success', $msg);
    };
    Route::post('commerce/branches', fn (Request $request) => $saveBranch($request));
    Route::post('commerce/branches/{id}', fn (Request $request, $id) => $saveBranch($request, $id))->whereNumber('id');
    Route::get('commerce/branches/{id}/delete', function ($id) {
        DB::table('commerce_branches')->where('id', $id)->delete();
        return redirect(url('bp-admin/commerce/branches'))->with('success', 'Location deleted.');
    })->whereNumber('id');
});

/* ---------------- Front: /shop ---------------- */
Route::middleware('web')->get('shop', function () {
    abort_unless(bp_plugin_option('commerce', 'shop_enabled', 'yes') === 'yes', 404);

    $products = Schema::hasTable('commerce_products')
        ? DB::table('commerce_products')->where('is_active', 1)
            ->orderBy('sort_order')->orderByDesc('id')->paginate(12)
        : collect();

    return view('commerce::shop', [
        'products'  => $products,
        'currency'  => bp_plugin_option('commerce', 'currency', 'MMK'),
        'themeSlug' => bp_option('theme', 'default'),
    ]);
});
