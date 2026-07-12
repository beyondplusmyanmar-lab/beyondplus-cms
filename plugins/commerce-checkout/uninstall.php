<?php

/**
 * Cleanup after migrations are rolled back on uninstall (idempotent safety net).
 */

use Illuminate\Support\Facades\Schema;

Schema::dropIfExists('commerce_order_items');
Schema::dropIfExists('commerce_orders');
