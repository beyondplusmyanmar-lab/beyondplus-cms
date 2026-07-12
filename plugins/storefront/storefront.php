<?php

/**
 * Storefront Setup — companion plugin for the Storefront theme.
 *
 * There are no runtime hooks: the theme handles presentation and the Commerce /
 * Commerce Checkout plugins provide /shop, /cart and orders. This plugin's only
 * job is its migration, which seeds the starter pages + menu on activation and
 * rolls them back on uninstall (see migrations/).
 */
