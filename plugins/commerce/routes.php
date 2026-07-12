<?php

/**
 * Commerce routes — loaded only while the plugin is active.
 *  - /bp-admin/commerce*  admin product CRUD ('admins' = full web stack + CSRF + admin auth)
 *  - /shop                front catalogue, rendered in the active theme ('web')
 *
 * Uses the query builder (no plugin-autoloaded model classes) and never deletes
 * files, keeping the plugin clear of the activation security scan.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/* ---------------- Admin: product management ---------------- */
Route::middleware('admins')->prefix('bp-admin')->group(function () {

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
    });

    // Shared store/update handler.
    $save = function (Request $request, $id = null) {
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

        // New upload replaces the stored filename; we never delete the old file
        // (file deletion is blocked by the plugin security scan).
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time().'_'.Str::random(6).'.'.strtolower($file->getClientOriginalExtension());
            $file->move(public_path('uploads'), $name);
            $row['image'] = $name;
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

    Route::post('commerce', fn (Request $request) => $save($request));
    Route::post('commerce/{id}', fn (Request $request, $id) => $save($request, $id));

    Route::get('commerce/{id}/delete', function ($id) {
        DB::table('commerce_products')->where('id', $id)->delete();
        return redirect(url('bp-admin/commerce'))->with('success', 'Product deleted.');
    });
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
