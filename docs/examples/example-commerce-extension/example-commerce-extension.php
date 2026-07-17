<?php

/**
 * Example: Today's Takings — the smallest useful commerce extension.
 *
 * What it demonstrates:
 *  - consuming the Orders API through the HOOK surface (doeh_list_orders), so
 *    there is no class dependency and the page degrades gracefully when the
 *    connector is off (the filter returns the passed default, null);
 *  - Model A: this plugin never touches the merchant key — the connector owns
 *    it. Nothing here could leak a credential;
 *  - a BOUNDED window (the API refuses unbounded lists) and branching on the
 *    STABLE code, including EDGE_RESULT_TOO_LARGE (refuse ≠ truncate);
 *  - money handling: report rows carry total.amount_minor with an
 *    AUTHORITATIVE scale — format with it, never guess decimals.
 *
 * This file registers nothing but the admin route; see routes.php.
 */

require __DIR__.'/routes.php';
