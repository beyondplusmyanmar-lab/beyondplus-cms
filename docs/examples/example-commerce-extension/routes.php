<?php

use Illuminate\Support\Facades\Route;

Route::middleware('admins')->prefix('bp-admin')->group(function () {

    Route::get('example-takings', function () {
        // Today, UTC, as the half-open window [00:00Z, tomorrow 00:00Z).
        $from = gmdate('Y-m-d\T00:00:00\Z');
        $to = gmdate('Y-m-d\T00:00:00\Z', strtotime('tomorrow UTC'));

        // The hook surface: returns the connector's normalized array, or the
        // default (null) when doeh-commerce is off/unconfigured.
        $result = bp_apply_filters('doeh_list_orders', null, [
            'from' => $from, 'to' => $to, 'limit' => 200,
        ]);

        $summary = null;
        $error = null;
        if ($result === null) {
            $error = 'DOEH Commerce is not configured.';
        } elseif (! ($result['ok'] ?? false)) {
            // ALWAYS branch on the stable code, never the human text.
            $code = $result['code'] ?? 'EDGE_TRANSPORT';
            $error = $code === 'EDGE_RESULT_TOO_LARGE'
                ? 'More than 200 orders today — narrow the window for a full report.'
                : "Could not load orders [{$code}].";
        } else {
            // Sum per currency using each row's OWN scale (0 for MMK — whole
            // units; 2 for USD-style). Never assume /100.
            $count = 0;
            $byCurrency = [];
            foreach ($result['orders'] ?? [] as $o) {
                $count++;
                $t = $o['total'] ?? null;
                if (! isset($t['amount_minor'])) {
                    continue;
                }
                $cur = (string) ($t['currency'] ?? '?');
                $byCurrency[$cur]['minor'] = ($byCurrency[$cur]['minor'] ?? 0) + (int) $t['amount_minor'];
                $byCurrency[$cur]['scale'] = (int) ($t['scale'] ?? 0);
            }
            $totals = [];
            foreach ($byCurrency as $cur => $v) {
                $totals[$cur] = number_format($v['minor'] / (10 ** $v['scale']), $v['scale']);
            }
            $summary = ['count' => $count, 'totals' => $totals];
        }

        return view('example-commerce-extension::page', [
            'error'   => $error,
            'summary' => $summary,
        ]);
    });
});
