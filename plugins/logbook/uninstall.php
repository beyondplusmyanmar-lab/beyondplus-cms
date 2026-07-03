<?php

/**
 * Optional uninstall cleanup, run after the plugin's migrations are rolled back.
 * The bp_logbook table is already dropped by the migration's down(); this is
 * where a plugin would remove any leftover options, files or cache entries.
 */

use Illuminate\Support\Facades\Schema;

Schema::dropIfExists('bp_logbook'); // idempotent safety net
