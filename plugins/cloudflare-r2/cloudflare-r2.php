<?php

/**
 * Cloudflare R2 (S3-compatible) storage plugin.
 *
 * - store_upload: when active + configured, uploads images to R2 with a signed
 *   (AWS SigV4) PUT and returns their public URL; a failure returns null so the
 *   core stores the file locally instead (uploads never break).
 * - delete_upload: removes the matching object from R2 when the CMS deletes an
 *   image whose URL belongs to this bucket.
 *
 * No AWS SDK required — requests are signed by hand. Works with any
 * S3-compatible endpoint (R2, AWS S3, …).
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Current config, or null when not fully configured. */
$r2_config = function () {
    $c = [
        'endpoint'   => rtrim((string) bp_plugin_option('cloudflare-r2', 'endpoint'), '/'),
        'bucket'     => trim((string) bp_plugin_option('cloudflare-r2', 'bucket'), '/'),
        'access_key' => (string) bp_plugin_option('cloudflare-r2', 'access_key'),
        'secret_key' => (string) bp_plugin_option('cloudflare-r2', 'secret_key'),
        'public_url' => rtrim((string) bp_plugin_option('cloudflare-r2', 'public_url'), '/'),
        'region'     => bp_plugin_option('cloudflare-r2', 'region') ?: 'auto',
    ];
    if ($c['endpoint'] === '' || $c['bucket'] === '' || $c['access_key'] === '' || $c['secret_key'] === '') {
        return null;
    }
    return $c;
};

/** AWS SigV4-signed request to R2/S3. Returns the HTTP response or null. */
$r2_send = function (string $method, string $key, string $body, array $c) {
    $host        = parse_url($c['endpoint'], PHP_URL_HOST);
    $path        = '/'.$c['bucket'].'/'.$key;
    $amzDate     = gmdate('Ymd\THis\Z');
    $dateStamp   = gmdate('Ymd');
    $payloadHash = hash('sha256', $body);

    $canonicalHeaders = "host:{$host}\nx-amz-content-sha256:{$payloadHash}\nx-amz-date:{$amzDate}\n";
    $signedHeaders    = 'host;x-amz-content-sha256;x-amz-date';
    $canonicalRequest = "{$method}\n{$path}\n\n{$canonicalHeaders}\n{$signedHeaders}\n{$payloadHash}";

    $scope        = "{$dateStamp}/{$c['region']}/s3/aws4_request";
    $stringToSign = "AWS4-HMAC-SHA256\n{$amzDate}\n{$scope}\n".hash('sha256', $canonicalRequest);

    $kDate    = hash_hmac('sha256', $dateStamp, 'AWS4'.$c['secret_key'], true);
    $kRegion  = hash_hmac('sha256', $c['region'], $kDate, true);
    $kService = hash_hmac('sha256', 's3', $kRegion, true);
    $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
    $signature = hash_hmac('sha256', $stringToSign, $kSigning);

    $headers = [
        'Authorization'        => "AWS4-HMAC-SHA256 Credential={$c['access_key']}/{$scope}, SignedHeaders={$signedHeaders}, Signature={$signature}",
        'x-amz-date'           => $amzDate,
        'x-amz-content-sha256' => $payloadHash,
    ];

    $req = Http::withHeaders($headers)->timeout(20);
    if ($body !== '') {
        $req = $req->withBody($body, 'application/octet-stream');
    }
    return $req->send($method, "{$c['endpoint']}{$path}");
};

bp_add_filter('store_upload', function ($stored, $file, $name) use ($r2_config, $r2_send) {
    if ($stored !== null) {
        return $stored;
    }
    $c = $r2_config();
    if (! $c) {
        return null; // not configured — local fallback
    }

    try {
        $body = @file_get_contents($file->getRealPath());
        if ($body === false) {
            return null;
        }
        $response = $r2_send('PUT', $name, $body, $c);
        if ($response->successful()) {
            return $c['public_url'] !== '' ? "{$c['public_url']}/{$name}" : "{$c['endpoint']}/{$c['bucket']}/{$name}";
        }
        Log::warning('R2 upload failed: HTTP '.$response->status().' — falling back to local.');
        return null;
    } catch (\Throwable $e) {
        Log::warning('R2 upload error: '.$e->getMessage().' — falling back to local.');
        return null;
    }
});

bp_add_filter('delete_upload', function ($handled, $value) use ($r2_config, $r2_send) {
    if ($handled) {
        return $handled;
    }
    $c = $r2_config();
    // Only handle values that are this bucket's public URLs.
    if (! $c || $c['public_url'] === '' || strpos((string) $value, $c['public_url'].'/') !== 0) {
        return $handled;
    }

    $key = ltrim(substr((string) $value, strlen($c['public_url'])), '/');
    try {
        $r2_send('DELETE', $key, '', $c);
    } catch (\Throwable $e) {
        Log::warning('R2 delete error: '.$e->getMessage());
    }
    return true; // claimed — it's our object
});
