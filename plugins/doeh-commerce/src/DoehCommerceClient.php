<?php

namespace Doeh\Commerce;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * DOEH Orders API connector — the whole job of this plugin.
 *
 * ── MODEL A (the credential invariant this class exists to enforce): the ONLY
 *    credential it ever sends to DOEH is the MERCHANT secret key, held
 *    server-side. There is no method, parameter, or code path that accepts a
 *    customer's OAuth/bearer token and uses it as the order credential. A
 *    customer's identity is not a merchant's authorization; conflating them
 *    (Model B) is forbidden, not unimplemented. If you find yourself wanting to
 *    pass a customer token in here, the answer is no.
 *
 * What it owns (and nothing else): API auth, request signing (Idempotency-Key on
 * creates), payload mapping, error normalization, and order retrieval. It has NO
 * UI and knows nothing about carts, catalogs, prices or checkout — the server
 * prices every submission from {sku, qty}, and the THEME owns all presentation.
 *
 * Every method returns a normalized array — never throws for an API-level error:
 *   success: ['ok' => true, 'status' => 2xx, 'order' => [...], 'idempotent' => bool]
 *   failure: ['ok' => false, 'status' => int, 'code' => 'EDGE_…'|'API_KEY_…', 'step' => ?string]
 * so a theme can branch on ['ok'] and show its own copy for a stable ['code'].
 */
final class DoehCommerceClient
{
    private const HOSTS = [
        'sandbox'    => 'https://sandbox-api.doehpos.com',
        'production' => 'https://api.doehpos.com',
    ];

    /** Fulfilment kinds the Orders API accepts (mirrors the SDK's FulfillmentType). */
    private const FULFILLMENT = ['pickup', 'delivery', 'dine_in'];

    public function __construct(
        private readonly string $secretKey,
        private readonly string $environment = 'sandbox',
        private readonly string $defaultFulfillment = '',
        private readonly int $timeoutSeconds = 15,
    ) {}

    /** The API host for this environment (sandbox unless explicitly production). */
    private function baseUrl(): string
    {
        return self::HOSTS[$this->environment] ?? self::HOSTS['sandbox'];
    }

    /**
     * POST /v1/orders — submit a sale by line items. The server resolves SKUs,
     * prices, tax, discounts and totals; the caller sends WHAT and HOW MANY only.
     *
     * @param array{lines: array<int, array{sku:string, qty:int, modifier_ids?:array<int,string>}>,
     *              customer?: array{phone?:string},
     *              fulfillment?: array{type?:string}} $submission
     * @param string|null $idempotencyKey Reuse across retries to make the create
     *        safe to repeat; auto-generated per call when omitted. To dedupe a
     *        real basket, pass a key derived from the cart, not a fresh one.
     */
    public function createOrder(array $submission, ?string $idempotencyKey = null): array
    {
        $body = $this->mapSubmission($submission);
        if (empty($body['lines'])) {
            // Fail before burning a request — the same structural rule the SDK checks.
            return ['ok' => false, 'status' => 0, 'code' => 'EDGE_EMPTY_ORDER', 'step' => 'client_validation'];
        }

        return $this->send('POST', '/v1/orders', [
            'json'           => $body,
            'idempotencyKey' => $idempotencyKey ?: $this->newIdempotencyKey(),
        ]);
    }

    /** GET /v1/orders/{id} — read a submitted order (resolved lines + totals). */
    public function getOrder(string $id): array
    {
        if (! preg_match('/^[A-Za-z0-9_]+$/', $id)) {
            return ['ok' => false, 'status' => 0, 'code' => 'EDGE_BAD_BODY', 'step' => 'client_validation'];
        }

        return $this->send('GET', '/v1/orders/'.$id);
    }

    /**
     * GET /v1/orders — a BOUNDED time-window report, not an unbounded list. The
     * Orders API requires a [from, to) window and refuses (rather than truncates)
     * a result larger than the limit, so callers get a whole answer or an error.
     *
     * @param array{from:string, to:string, limit?:int, branch_id?:int, status?:string} $query
     *        from/to are ISO-8601/RFC-3339 timestamps and are REQUIRED.
     */
    public function listOrders(array $query): array
    {
        if (empty($query['from']) || empty($query['to'])) {
            return ['ok' => false, 'status' => 0, 'code' => 'EDGE_BAD_BODY', 'step' => 'client_validation'];
        }

        $params = array_filter([
            'from'      => (string) $query['from'],
            'to'        => (string) $query['to'],
            'limit'     => isset($query['limit']) ? (int) $query['limit'] : null,
            'branch_id' => isset($query['branch_id']) ? (int) $query['branch_id'] : null,
            'status'    => isset($query['status']) ? (string) $query['status'] : null,
        ], fn ($v) => $v !== null && $v !== '');

        return $this->send('GET', '/v1/orders', ['query' => $params]);
    }

    // ── request mapping ────────────────────────────────────────────────────────

    /**
     * Shape a caller's submission into the exact wire body, dropping anything the
     * API does not accept. Prices, currency and totals are intentionally NOT
     * mappable — the server is the sole source of truth for money.
     *
     * @param array<string,mixed> $in
     * @return array<string,mixed>
     */
    private function mapSubmission(array $in): array
    {
        $lines = [];
        foreach ((array) ($in['lines'] ?? []) as $line) {
            $sku = isset($line['sku']) ? (string) $line['sku'] : '';
            $qty = isset($line['qty']) ? (int) $line['qty'] : 0;
            if ($sku === '' || $qty < 1) {
                continue; // skip malformed lines; an all-empty basket is caught by the caller
            }
            $mapped = ['sku' => $sku, 'qty' => $qty];
            if (! empty($line['modifier_ids']) && is_array($line['modifier_ids'])) {
                $mapped['modifier_ids'] = array_values(array_map('strval', $line['modifier_ids']));
            }
            $lines[] = $mapped;
        }

        $body = ['lines' => $lines];

        $phone = $in['customer']['phone'] ?? null;
        if (is_string($phone) && $phone !== '') {
            $body['customer'] = ['phone' => $phone];
        }

        $type = $in['fulfillment']['type'] ?? $this->defaultFulfillment;
        if (is_string($type) && in_array($type, self::FULFILLMENT, true)) {
            $body['fulfillment'] = ['type' => $type];
        }

        return $body;
    }

    // ── transport + error normalization ─────────────────────────────────────────

    /**
     * @param array{json?:array<string,mixed>, query?:array<string,mixed>, idempotencyKey?:string} $opts
     * @return array<string,mixed>
     */
    private function send(string $method, string $path, array $opts = []): array
    {
        if ($this->secretKey === '') {
            return ['ok' => false, 'status' => 0, 'code' => 'API_KEY_INVALID', 'step' => 'not_configured'];
        }

        try {
            $req = Http::withToken($this->secretKey)             // Authorization: Bearer sk_…
                ->acceptJson()
                ->withHeaders(['User-Agent' => 'doeh-commerce-cms/0.1.0'])  // api vhost 403s an empty UA
                ->timeout($this->timeoutSeconds);

            if (isset($opts['idempotencyKey'])) {
                $req = $req->withHeaders(['Idempotency-Key' => $opts['idempotencyKey']]);
            }

            $url = $this->baseUrl().$path;
            $response = $method === 'POST'
                ? $req->post($url, $opts['json'] ?? [])
                : $req->get($url, $opts['query'] ?? []);
        } catch (\Throwable $e) {
            // Never leak the key or full URL into logs.
            Log::warning('[doeh-commerce] transport error: '.$e->getMessage());
            return ['ok' => false, 'status' => 0, 'code' => 'EDGE_TRANSPORT', 'step' => 'transport'];
        }

        $status = $response->status();
        $data   = $response->json();

        if ($response->successful()) {
            return [
                'ok'         => true,
                'status'     => $status,
                'idempotent' => (bool) ($data['idempotent'] ?? false),  // 200 replay vs 201 first write
                'order'      => $data['order'] ?? $data,
                'orders'     => $data['orders'] ?? null,                 // present on the list report
            ];
        }

        // Non-2xx: surface the stable UPPER_SNAKE code the API returned, unparsed.
        return [
            'ok'     => false,
            'status' => $status,
            'code'   => is_array($data) && isset($data['code']) ? (string) $data['code'] : 'HTTP_'.$status,
            'step'   => is_array($data) ? ($data['step'] ?? null) : null,
        ];
    }

    private function newIdempotencyKey(): string
    {
        return 'cms_'.Str::uuid()->toString();
    }
}
