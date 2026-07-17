# DOEH Setup Wizard

The merchant onboarding flow (**Admin → DOEH Setup**, `/bp-admin/doeh-setup`):
from an empty CMS to a storefront taking real DOEH orders, without developer
help. Six re-entrant steps — each one's done-ness is computed from live config,
so the wizard survives partial runs and can revisit any step.

1. **Plugins** — activates DOEH Identity, Commerce and Commerce Storefront (in
   dependency order, through the loader's enforced activation).
2. **Theme** — the DOEH vertical themes (any theme whose manifest requires
   `doeh-commerce-storefront`), each shown with its declared fulfilment badges.
   The theme *is* the business model — fulfilment is displayed, not chosen (D2).
3. **Branding** — the active theme's Brand-group settings, saved through the
   same storage as the Theme Customize page.
4. **Commerce key** — paste the merchant `sk_`; the wizard **proves it against
   the live Orders API** (a tiny bounded window report) before saving. A key
   that fails is never stored, and a saved key is never echoed back.
5. **Customer sign-in** *(optional)* — DOEH Identity `client_id` + `pk_`,
   format-validated and saved, or **skipped** (commerce works without it; add
   it later by re-running this step).
6. **Done** — the live checklist, with links to the storefront, the Orders
   dashboard and the Customize page.

## Boundaries

- **Keys are collected and validated, never minted.** Issuance is the DOEH
  developer portal's job; this wizard has no code path that creates a
  credential.
- The `sk_` cross-check (prefix vs environment) runs before any network call;
  the live proof decides the rest — the wizard trusts the API's stable codes,
  not its own guesses.
- Requires nothing to activate: it bootstraps the bridge plugins itself.
