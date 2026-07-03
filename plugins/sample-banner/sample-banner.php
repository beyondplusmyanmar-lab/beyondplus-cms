<?php

/**
 * Sample Banner — a minimal demo plugin.
 *
 * Registers a callback on the `theme_footer` action so the front-end footer
 * shows a small line. This file is loaded only when the plugin is active.
 */

bp_add_action('theme_footer', function () {
    echo '<div class="text-center small py-2" style="color:#94a3b8;">'
        . '✨ Powered by the <strong>Sample Banner</strong> plugin</div>';
});
