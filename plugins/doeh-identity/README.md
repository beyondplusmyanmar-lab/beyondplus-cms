# DOEH Identity

Let visitors of a merchant website sign in with their DOEH customer identity
(hosted OAuth 2.1 + PKCE) and see a member area — sign-in plus a read-only
loyalty dashboard. Wallet and profile always open on DOEH-hosted pages; this
plugin never renders them.

## How it works (and the one rule that matters)

The plugin is a **browser-only public OAuth client**. The PHP side renders
mount points, a callback page, and public configuration — nothing else. The
entire identity flow (PKCE, the code exchange, token storage, refresh rotation,
sign-out revoke) runs in `assets/doeh-identity.js` in the customer's browser.

**No customer token ever touches the CMS server.** There is no server endpoint
that accepts, stores, logs, or forwards an access or refresh token, and adding
one is an architecture change, not a feature. The access token lives in browser
memory only; the refresh token lives in `sessionStorage` (per tab, dies with the
tab). Nothing identity-related is written to the CMS database or to cookies.

## Setup

1. Get an OAuth **Client ID** (`app_…`) and **Publishable Key** (`pk_…`) for
   this website from DOEH. One website = one DOEH Application; never reuse
   another site's client.
2. Ask DOEH to register, for that client:
   - redirect URI: `https://<your-site>/doeh/callback`
   - browser origin: `https://<your-site>`
3. Activate the plugin, then fill in **Plugins → DOEH Identity → Settings**:
   Environment (sandbox/production), Client ID, Publishable Key.

Both configured values are public identifiers by design — they appear in page
source on purpose and grant nothing without the customer's own sign-in.

## Placing the widgets

- In page/post content: `[doeh_signin]` and `[doeh_loyalty]`
- In theme markup: `<div data-doeh-widget="signin"></div>` /
  `<div data-doeh-widget="loyalty"></div>`
- Via theme filters:
  `{!! bp_apply_filters('doeh_signin_button', '') !!}` /
  `{!! bp_apply_filters('doeh_loyalty_panel', '') !!}`

## For theme authors

`window.DoehIdentity` is available on every page while the plugin is enabled:

| Call | Meaning |
|---|---|
| `signIn()` | start the hosted-login redirect |
| `signOut()` | revoke the session and re-render widgets |
| `isSignedIn()` | whether a customer session exists in this tab |
| `getCustomer()` | resolves `{ state, customerId, pointsBalance }` (cached; pass `true` to refetch) or `null` when signed out |
| `getCustomerToken()` | resolves an access token (or `null`) — for DOEH consumer API calls **only** |
| `render()` | re-render all mount points |

The plugin fires `doeh:identity` on `document` whenever the session state
changes (boot, sign-out) — listen to it to re-render your own account UI.

`getCustomer()` intentionally has **no name/phone/profile fields**: profile
data is a sensitive scope reserved for DOEH's native apps platform-wide. Link
to the DOEH-hosted profile page instead of trying to render one.

Never persist the token `getCustomerToken()` returns and never send it to any
host other than the DOEH consumer API — including your own server.

## v1 scope

Sign-in + loyalty dashboard, read-only. No redeem, no wallet, no profile
editing — those are DOEH-hosted experiences the plugin links out to.
