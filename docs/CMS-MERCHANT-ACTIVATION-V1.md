# CMS Merchant Activation v1

**Decided 2026-07-17.** The commerce/identity foundation is frozen and proven
([CMS-COMMERCE-V1-FREEZE.md](./CMS-COMMERCE-V1-FREEZE.md)); this milestone moves
from *building foundation* to **activating the existing product surface for
merchants**. It opens no new platform API, no identity work, and no v2 contract.

**Success criterion:** a restaurant owner can install a DOEH CMS site, choose a
theme, configure it, receive a real DOEH order, and manage it — without
developer help. (Provable with commerce only; hosted identity stays an optional
add-on gated on its own platform activation.)

## Frozen decisions

### D1 — Tenancy: single merchant per CMS installation

A merchant website **is a CMS installation**, not a tenant inside a shared CMS.
Each merchant runs their own install with its own DB, theme, plugins and
`sk_live_` — matching the current architecture and keeping the security
boundary simple (no tenant isolation, routing, key-ownership or storage-sharding
work). Provisioning is manual in v1; a multi-tenant `cms-cloud` is a **separate
future architecture project**, opened only on real demand.

### D2 — Fulfilment: theme-declared in v1

The theme's manifest `fulfillment_types` stays the single source of what the
storefront offers — **no merchant override in v1**. The theme represents the
business model (a restaurant knows dine-in; a service vertical opts out), and a
merchant override would make the CMS responsible for validating business
capabilities (e.g. a service theme with delivery enabled and no delivery
domain). Merchants choose capability by choosing a theme. A
merchant-settings + platform-capability + theme-recommendation model may be
introduced **later**, when fulfilment execution becomes a platform capability.

## Phases

| Phase | What | Status |
|---|---|---|
| 1 | **Merchant operations dashboard** — admin Orders page (list via the bounded window report, search by id, detail; fulfilment shown when the API reports it). Read-only consumption of `GET /v1/orders` + `GET /v1/orders/{id}` through the existing connector — no platform change. | ✅ built (`doeh-commerce-storefront` 0.3.0) |
| 2 | **Theme enhancement pass** — enrich the four DOEH themes' EXISTING settings schemas (logo, favicon, brand knobs, section toggles). Rides the Theme Customize page shipped in CMS 2.5.0 — not a new customizer. | ✅ built (all four themes: logo + favicon + hero/rewards toggles; retail grid density) |
| 3 | **Merchant setup wizard** — install → select theme → configure branding → paste `sk_live_` → live-validate against the Orders API → preview → publish. Keys are **collected and validated, never minted** (issuance is the DOEH developer portal's job). Identity (`pk_`/`client_id`) is an **optional, skippable** step — commerce works without it. | pending |
| 4 | **Developer examples** — `docs/examples/`: loyalty widget, minimal theme, commerce extension. The guides and contracts already exist. | pending |
| 5 | **First merchant deployment** — one real merchant: own install, real domain, real `sk_live_`, real orders. | pending (gated on 1–4) |

## Explicitly out of scope

- Commerce v2 (catalog / inventory / webhooks / delivery / payments) — each is a
  separate contract, opened when real merchants hit the limit, never combined.
- Multi-tenant CMS SaaS (see D1).
- Merchant fulfilment override (see D2).
- Hosted-identity prod activation (E5-C chain) — its own platform lane; the
  wizard treats identity as "add later".
