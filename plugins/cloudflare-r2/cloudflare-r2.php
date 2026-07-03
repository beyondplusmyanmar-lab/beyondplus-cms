<?php

/**
 * Cloudflare R2 (S3-compatible) storage plugin.
 *
 * Registers the `store_upload` hook: when this plugin is active AND configured,
 * uploaded images are pushed to R2 with a signed (AWS Signature V4) PUT and the
 * hook returns their public URL. If it isn't configured or the upload fails, the
 * hook returns null and the core stores the file locally instead — so uploads
 * never break. Works with any S3-compatible endpoint (R2, AWS S3, …).
 */

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

bp_add_filter('store_upload', function ($stored, $file, $name) {
    if ($stored !== null) {
        return $stored; // already handled by another storage plugin
    }

    $endpoint  = rtrim((string) bp_plugin_option('cloudflare-r2', 'endpoint'), '/');
    $bucket    = trim((string) bp_plugin_option('cloudflare-r2', 'bucket'), '/');
    $accessKey = (string) bp_plugin_option('cloudflare-r2', 'access_key');
    $secretKey = (string) bp_plugin_option('cloudflare-r2', 'secret_key');
    $publicUrl = rtrim((string) bp_plugin_option('cloudflare-r2', 'public_url'), '/');
    $region    = bp_plugin_option('cloudflare-r2', 'region') ?: 'auto';

    if ($endpoint === '' || $bucket === '' || $accessKey === '' || $secretKey === '') {
        return null; // not configured — fall back to local storage
    }

    try {
        $body = @file_get_contents($file->getRealPath());
        if ($body === false) {
            return null;
        }
        $contentType = $file->getMimeType() ?: 'application/octet-stream';

        $host        = parse_url($endpoint, PHP_URL_HOST);
        $path        = '/'.$bucket.'/'.$name;
        $amzDate     = gmdate('Ymd\THis\Z');
        $dateStamp   = gmdate('Ymd');
        $payloadHash = hash('sha256', $body);

        // --- AWS Signature V4 (service: s3) ---------------------------------
        $canonicalHeaders = "host:{$host}\nx-amz-content-sha256:{$payloadHash}\nx-amz-date:{$amzDate}\n";
        $signedHeaders    = 'host;x-amz-content-sha256;x-amz-date';
        $canonicalRequest = "PUT\n{$path}\n\n{$canonicalHeaders}\n{$signedHeaders}\n{$payloadHash}";

        $scope        = "{$dateStamp}/{$region}/s3/aws4_request";
        $stringToSign = "AWS4-HMAC-SHA256\n{$amzDate}\n{$scope}\n".hash('sha256', $canonicalRequest);

        $kDate    = hash_hmac('sha256', $dateStamp, 'AWS4'.$secretKey, true);
        $kRegion  = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $authorization = "AWS4-HMAC-SHA256 Credential={$accessKey}/{$scope}, "
            ."SignedHeaders={$signedHeaders}, Signature={$signature}";

        $response = Http::withHeaders([
            'Authorization'        => $authorization,
            'x-amz-date'           => $amzDate,
            'x-amz-content-sha256' => $payloadHash,
        ])->withBody($body, $contentType)->timeout(20)->put("{$endpoint}{$path}");

        if ($response->successful()) {
            return $publicUrl !== '' ? "{$publicUrl}/{$name}" : "{$endpoint}{$path}";
        }

        Log::warning('R2 upload failed: HTTP '.$response->status().' — falling back to local.');
        return null;
    } catch (\Throwable $e) {
        Log::warning('R2 upload error: '.$e->getMessage().' — falling back to local.');
        return null;
    }
});
