<?php

/**
 * Optional cleanup, run after the plugin's migrations are rolled back on
 * uninstall. The commerce_products table is already dropped by the migration's
 * down(); this is an idempotent safety net. Product images left in
 * public/uploads are intentionally kept (the plugin never deletes files).
 */

use Illuminate\Support\Facades\Schema;

Schema::dropIfExists('commerce_products');
