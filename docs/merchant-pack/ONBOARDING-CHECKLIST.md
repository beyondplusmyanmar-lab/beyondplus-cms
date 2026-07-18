# Merchant Onboarding Checklist

Operator-facing tracker for taking one merchant from "yes" to a running
production storefront. The technical authority for every step is
[MERCHANT-DEPLOYMENT-V1.md](../MERCHANT-DEPLOYMENT-V1.md) — this checklist
sequences it and adds what to collect from the merchant.

One merchant = one CMS installation = one `sk_live_`.

---

## A. Collect from the merchant (before touching a server)

- [ ] Business name (as it should appear on the site)
- [ ] Business category → theme: restaurant / retail / service / business
- [ ] Logo (and favicon if they have one; otherwise derive from logo)
- [ ] Brand colour(s), if they care
- [ ] Product / menu / service list **with prices and currency**
- [ ] Contact details for the site (phone, address, hours)
- [ ] Domain: their own, or agree a name to register
- [ ] Admin owner: who at the shop will operate the dashboard (name + email)
- [ ] How they handle orders today (informs the step-8 acceptance question)

## B. Platform prerequisites (operator, DOEH side)

- [ ] Merchant exists as a **shop** on the DOEH platform
- [ ] Products/menu loaded and priced in the shop's currency
- [ ] `sk_live_` minted via the developer portal (`api-client:mint`),
      scopes `orders:read,orders:write` — delivered out-of-band, never
      transcribed into chat/docs
- [ ] (Optional, only if identity is activated for this merchant)
      `client_id` + `pk_live_` — otherwise skip; commerce works without it

## C. Instance deployment (runbook §2–§3)

- [ ] Server/hosting ready: PHP 8.3+, Composer 2, MySQL/MariaDB
- [ ] Clone + `make install` (`.env` from `.env.production.example`,
      DB set **before** install)
- [ ] Preflight smoke checklist from the runbook passes (rehearse with a
      sandbox `sk_test_` if this is an unfamiliar host; revoke + reset after)
- [ ] Admin login works; seeded password **changed**; credentials handed to
      the merchant's admin owner only

## D. Setup wizard (runbook §4 — merchant drives, operator guides)

- [ ] Plugins page: activate **DOEH Setup Wizard**
- [ ] Step 1 — plugins activated in dependency order
- [ ] Step 2 — vertical theme selected (theme = business model; fulfilment
      badges shown match the merchant's reality)
- [ ] Step 3 — logo, favicon, branding configured
- [ ] Step 4 — `sk_live_` pasted and **live-proved** against the Orders API
- [ ] Step 5 — customer sign-in configured **or** skipped
- [ ] Step 6 — done checklist reached
- [ ] **Admin → Commerce → Orders** loads from the merchant's seat
      (empty list = pass)

## E. Content & publish (runbook §6–§7)

- [ ] Theme Customize: sections, hero, menu/products/services filled in —
      the site reads as *the merchant's*, not a template
- [ ] DNS pointed, HTTPS serving; trusted-proxy set if behind a CDN
- [ ] Cold check of the public URL: storefront renders, checkout reaches the
      Orders API, no mixed content

## F. Acceptance — the first order (runbook §8–§9)

Run [FIRST-ORDER-WALKTHROUGH.md](./FIRST-ORDER-WALKTHROUGH.md) with the
merchant driving. Then record the evidence table:

- [ ] Customer order placed from a real phone on a public network
- [ ] Merchant found and opened the order on their dashboard unaided
- [ ] Merchant stated, unprompted, what they'd do next with the order
- [ ] **Verbatim** list of everything they asked for that doesn't exist —
      this list is the only input that opens Commerce v2 work

## G. Handover

- [ ] Merchant has: admin URL + their credentials, storefront URL,
      operator contact for support
- [ ] Operator has: evidence table filed, feature-ask list filed,
      key inventory updated
- [ ] Agree the support expectation (what they call you for vs. do themselves)
