/*
 * doeh-identity.js — the browser OAuth 2.1 + PKCE core for the DOEH Identity CMS
 * plugin. Self-contained, no dependencies, no CDN.
 *
 * This file is the ENTIRE identity surface of the plugin. The PHP shell only
 * renders mount points and public config (invariant P1: no token ever touches
 * the server). Everything security-relevant — PKCE, the code exchange, token
 * storage, refresh rotation, revoke — happens here, in the customer's browser.
 *
 * Token lifecycle (ADR D4, as ACCEPTED): the access token lives in MEMORY ONLY
 * and dies on every navigation (this CMS is multipage); only the refresh token
 * is persisted, in sessionStorage, per-tab. Each page load re-mints the access
 * token through one rotation call. Per-tab families mean one-time-use rotation
 * never races itself. Nothing is written to localStorage or cookies.
 */
(function () {
  "use strict";

  var CFG = window.__DOEH_IDENTITY__;
  if (!CFG || !CFG.issuer || !CFG.clientId) return; // not configured → do nothing

  var TOK_KEY = "doeh.id.tokens.v1";
  var PKCE_KEY = "doeh.id.pkce.v1";
  var DISC_KEY = "doeh.id.disc.v1";
  var SKEW_MS = 30000; // treat a token as expired 30s early

  // Minimal EN/MY copy. The CMS locale is on <html lang>; MY when it starts "my".
  var MY = (document.documentElement.lang || "").toLowerCase().indexOf("my") === 0;
  var T = {
    signIn: MY ? "DOEH ဖြင့် ဝင်ရန်" : "Sign in with DOEH",
    signOut: MY ? "ထွက်ရန်" : "Sign out",
    signedInAs: MY ? "ဝင်ရောက်ထားသည်" : "Signed in",
    points: MY ? "အမှတ်များ" : "Points",
    loading: MY ? "ဖွင့်နေသည်…" : "Loading…",
    reconnect: MY ? "ပြန်လည်ချိတ်ဆက်ရန်" : "Reconnect",
    signInToView: MY ? "Loyalty ကြည့်ရန် ဝင်ပါ။" : "Sign in to view your loyalty.",
    genericErr: MY ? "တစ်ခုခု မှားယွင်းနေသည်။" : "Something went wrong.",
  };

  // ---- tiny helpers ---------------------------------------------------------
  function b64url(bytes) {
    var s = "";
    var b = new Uint8Array(bytes);
    for (var i = 0; i < b.length; i++) s += String.fromCharCode(b[i]);
    return btoa(s).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
  }
  function randB64url(n) {
    var a = new Uint8Array(n);
    crypto.getRandomValues(a);
    return b64url(a.buffer);
  }
  async function sha256(str) {
    var data = new TextEncoder().encode(str);
    return crypto.subtle.digest("SHA-256", data);
  }
  function form(obj) {
    return Object.keys(obj)
      .map(function (k) { return encodeURIComponent(k) + "=" + encodeURIComponent(obj[k]); })
      .join("&");
  }
  function ss() { try { return window.sessionStorage; } catch (e) { return null; } }

  // ---- token store (ADR D4) ---------------------------------------------------
  // Access token: memory only — this closure variable IS its entire lifetime.
  // Refresh token: sessionStorage, per-tab. That split is the signed decision;
  // persisting the access token re-opens D4, it is not an optimization.
  var mem = { accessToken: null, expiresAt: 0 };
  var refresh = {
    get: function () {
      var s = ss(); if (!s) return null;
      return s.getItem(TOK_KEY) || null;
    },
    set: function (rt) { var s = ss(); if (s) s.setItem(TOK_KEY, rt); },
    clear: function () { var s = ss(); if (s) s.removeItem(TOK_KEY); },
  };
  function acceptTokens(data) {
    mem.accessToken = data.access_token;
    mem.expiresAt = Date.now() + (Number(data.expires_in || 600) * 1000);
    if (data.refresh_token) refresh.set(data.refresh_token); // rotated family head
  }
  function dropSession() {
    mem.accessToken = null;
    mem.expiresAt = 0;
    customerCache = null;
    refresh.clear();
  }

  // Tell theme JS the session state changed (sign-out, boot, widget re-render).
  // Themes listen:  document.addEventListener('doeh:identity', fn)
  function announce() {
    try {
      document.dispatchEvent(new CustomEvent("doeh:identity", { detail: { signedIn: isSignedIn() } }));
    } catch (e) {}
  }

  // ---- discovery (cached per tab) -------------------------------------------
  async function discovery() {
    var s = ss();
    if (s) {
      var c = s.getItem(DISC_KEY);
      if (c) { try { return JSON.parse(c); } catch (e) {} }
    }
    var res = await fetch(CFG.issuer + "/.well-known/openid-configuration", {
      headers: { Accept: "application/json" },
    });
    if (!res.ok) throw new Error("discovery_failed_" + res.status);
    var disc = await res.json();
    if (s) s.setItem(DISC_KEY, JSON.stringify(disc));
    return disc;
  }

  // ---- the OAuth walk -------------------------------------------------------
  async function signIn() {
    var disc = await discovery();
    var verifier = randB64url(48);
    var challenge = b64url(await sha256(verifier));
    var state = randB64url(16);
    var returnPath = location.pathname + location.search + location.hash;
    var s = ss();
    if (s) s.setItem(PKCE_KEY, JSON.stringify({ verifier: verifier, state: state, returnPath: returnPath }));

    var url = disc.authorization_endpoint + "?" + form({
      response_type: "code",
      client_id: CFG.clientId,
      redirect_uri: CFG.redirectUri,
      code_challenge: challenge,
      code_challenge_method: "S256",
      scope: CFG.scope,
      state: state,
    });
    location.assign(url); // top-level redirect to the hosted DOEH login
  }

  async function handleCallback() {
    var params = new URLSearchParams(location.search);
    var code = params.get("code");
    var retState = params.get("state");
    var err = params.get("error");
    var s = ss();
    var pkce = null;
    try { pkce = JSON.parse((s && s.getItem(PKCE_KEY)) || "null"); } catch (e) {}
    if (s) s.removeItem(PKCE_KEY);

    var back = (pkce && pkce.returnPath) || "/";
    if (err) return fail(err, back);
    if (!code || !pkce || retState !== pkce.state) return fail("invalid_callback", back);

    try {
      var disc = await discovery();
      var res = await fetch(disc.token_endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded", Accept: "application/json" },
        body: form({
          grant_type: "authorization_code",
          code: code,
          code_verifier: pkce.verifier,
          redirect_uri: CFG.redirectUri,
          client_id: CFG.clientId,
        }),
      });
      var data = await res.json().catch(function () { return null; });
      if (!res.ok || !data || !data.access_token) {
        return fail((data && data.error) || "token_exchange_failed_" + res.status, back);
      }
      acceptTokens(data);
      location.replace(back); // clean the code out of history; memory access token
      // dies here BY DESIGN (D4) — the landing page re-mints via one rotation.
    } catch (e) {
      fail("network", back);
    }
  }

  function fail(reason, back) {
    var el = document.querySelector('[data-doeh-widget="callback"]');
    if (el) {
      el.innerHTML =
        '<p class="err">' + T.genericErr + "</p>" +
        '<p><a href="' + esc(back) + '">' + (MY ? "ပြန်သွားရန်" : "Go back") + "</a></p>";
    }
  }

  // Single-flight refresh: sessionStorage is per-tab so only this tab's family
  // is ever in play, but we still guard against two concurrent calls in one tab.
  // forceRotate skips the in-memory token — used when the edge said 401 on a
  // token we still believed valid (clock skew, server-side revoke).
  var refreshInFlight = null;
  async function getAccessToken(forceRotate) {
    if (!forceRotate && mem.accessToken && Date.now() < mem.expiresAt - SKEW_MS) {
      return mem.accessToken;
    }
    var rt = refresh.get();
    if (!rt) { dropSession(); return null; }
    if (!refreshInFlight) refreshInFlight = doRefresh(rt).finally(function () { refreshInFlight = null; });
    return refreshInFlight;
  }
  async function doRefresh(rt) {
    try {
      var disc = await discovery();
      var res = await fetch(disc.token_endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded", Accept: "application/json" },
        body: form({ grant_type: "refresh_token", refresh_token: rt, client_id: CFG.clientId }),
      });
      var data = await res.json().catch(function () { return null; });
      if (!res.ok || !data || !data.access_token) {
        // invalid_grant = burned/reused/revoked family — dead for good (D9).
        dropSession();
        return null;
      }
      acceptTokens(data); // rotation: new refresh token replaces the old head
      return mem.accessToken;
    } catch (e) {
      return null; // network hiccup: keep the stored refresh token, retry next call
    }
  }

  // ---- customer snapshot for themes -----------------------------------------
  // What this trust class may know about the signed-in customer: their loyalty
  // standing at THIS shop. No name, no phone, no profile — profile.pii is a
  // sensitive scope reserved for native clients platform-wide. Themes wanting
  // to show profile data link out to the DOEH-hosted page instead.
  var customerCache = null;
  async function getCustomer(force) {
    if (!isSignedIn()) return null;
    if (customerCache && !force) return customerCache;
    var r = await loyaltyBalance();
    if (r.state !== "ok") return { state: r.state };
    var d = (r.data && r.data.data) || {};
    customerCache = {
      state: "ok",
      customerId: d.customer_id != null ? d.customer_id : null,
      pointsBalance: d.points_balance != null ? d.points_balance : null,
    };
    return customerCache;
  }

  async function signOut() {
    var rt = refresh.get();
    dropSession();
    if (rt) {
      try {
        var disc = await discovery();
        await fetch(disc.revocation_endpoint || CFG.issuer + "/revoke", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: form({ token: rt, token_type_hint: "refresh_token", client_id: CFG.clientId }),
        });
      } catch (e) {}
    }
    render();
    announce();
  }

  // "Signed in" for widget purposes = we hold a live access token or a refresh
  // token that can mint one. The first data call is what proves it (D4).
  function isSignedIn() { return !!(mem.accessToken || refresh.get()); }

  // ---- consumer data plane (loyalty read) -----------------------------------
  // Direct fetch to the consumer edge with the SDK's wire contract (pk + bearer).
  // Extracting this into @beyondplusmm/doeh-consumer-sdk is deferred (ADR D7).
  async function loyaltyBalance() {
    var at = await getAccessToken();
    if (!at) return { state: "signedOut" };
    var res = await fetch(CFG.apiBase + "/mobile/loyalty/balance", {
      headers: { "X-Publishable-Key": CFG.publishableKey, Authorization: "Bearer " + at, Accept: "application/json" },
    });
    if (res.status === 401) {
      // Token the edge rejects but we believed valid: force ONE rotation (D9).
      var retry = await getAccessToken(true);
      if (!retry) return { state: "signedOut" };
      res = await fetch(CFG.apiBase + "/mobile/loyalty/balance", {
        headers: { "X-Publishable-Key": CFG.publishableKey, Authorization: "Bearer " + retry, Accept: "application/json" },
      });
      if (res.status === 401) { dropSession(); return { state: "signedOut" }; }
    }
    if (res.status === 404) return { state: "reconnect" }; // consent revoked (A4 invisibility)
    if (res.status === 429) return { state: "busy" };
    if (res.status === 403) {
      // ORIGIN_NOT_ALLOWED / key-env mismatch: an OPERATOR config error, never a
      // customer failure (D9). Log for the site admin, render nothing scary.
      console.error("[doeh-identity] configuration error from the DOEH edge (check registered origins / publishable key):", res.status);
      return { state: "config" };
    }
    if (!res.ok) return { state: "error" };
    var data = await res.json().catch(function () { return null; });
    return { state: "ok", data: data };
  }

  // ---- widgets --------------------------------------------------------------
  function esc(s) {
    return String(s == null ? "" : s).replace(/[&<>"']/g, function (c) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c];
    });
  }
  function btn(label, cls) {
    return '<button type="button" class="doeh-btn ' + cls + '" style="' +
      "display:inline-block;padding:9px 16px;border-radius:8px;border:0;cursor:pointer;" +
      "font:inherit;background:#6366f1;color:#fff;\">" + esc(label) + "</button>";
  }

  function renderSignin(el) {
    if (isSignedIn()) {
      el.innerHTML =
        '<span style="margin-right:8px;color:#475569;">' + T.signedInAs + "</span>" +
        btn(T.signOut, "doeh-signout");
      el.querySelector(".doeh-signout").onclick = signOut;
    } else {
      el.innerHTML = btn(T.signIn, "doeh-signin");
      el.querySelector(".doeh-signin").onclick = function () { signIn(); };
    }
  }

  async function renderLoyalty(el) {
    if (!isSignedIn()) { el.innerHTML = '<p style="color:#64748b;">' + T.signInToView + "</p>"; return; }
    el.innerHTML = '<p style="color:#64748b;">' + T.loading + "</p>";
    var r = await loyaltyBalance();
    if (r.state === "signedOut") { el.innerHTML = '<p style="color:#64748b;">' + T.signInToView + "</p>"; return; }
    if (r.state === "reconnect") {
      el.innerHTML = "<p>" + T.reconnect + " " + btn(T.signIn, "doeh-reconnect") + "</p>";
      el.querySelector(".doeh-reconnect").onclick = function () { signIn(); };
      return;
    }
    if (r.state === "busy") { el.innerHTML = '<p style="color:#64748b;">' + T.loading + "</p>"; return; }
    if (r.state === "config") { el.innerHTML = ""; return; } // admin-facing, already on console
    if (r.state !== "ok") { el.innerHTML = '<p style="color:#b91c1c;">' + T.genericErr + "</p>"; return; }
    // Envelope: { ok, data: { points_balance, ... } } — the consumer-SDK wire shape.
    var d = (r.data && r.data.data) || {};
    var pts = d.points_balance != null ? d.points_balance : "—";
    el.innerHTML =
      '<div style="padding:16px;border:1px solid #e2e8f0;border-radius:12px;display:inline-block;min-width:160px;">' +
      '<div style="font-size:13px;color:#64748b;">' + T.points + "</div>" +
      '<div style="font-size:28px;font-weight:700;color:#0f172a;">' + esc(pts) + "</div></div>";
  }

  // Replace literal [doeh_signin]/[doeh_loyalty] tokens in content with mount nodes.
  function expandShortcodes() {
    var map = { "[doeh_signin]": "signin", "[doeh_loyalty]": "loyalty" };
    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
    var hits = [];
    var n;
    while ((n = walker.nextNode())) {
      if (n.nodeValue && (n.nodeValue.indexOf("[doeh_signin]") >= 0 || n.nodeValue.indexOf("[doeh_loyalty]") >= 0)) {
        hits.push(n);
      }
    }
    hits.forEach(function (node) {
      var frag = document.createDocumentFragment();
      var parts = node.nodeValue.split(/(\[doeh_signin\]|\[doeh_loyalty\])/);
      parts.forEach(function (p) {
        if (map[p]) {
          var d = document.createElement("div");
          d.setAttribute("data-doeh-widget", map[p]);
          frag.appendChild(d);
        } else if (p) {
          frag.appendChild(document.createTextNode(p));
        }
      });
      node.parentNode.replaceChild(frag, node);
    });
  }

  function render() {
    document.querySelectorAll('[data-doeh-widget="signin"]').forEach(renderSignin);
    document.querySelectorAll('[data-doeh-widget="loyalty"]').forEach(renderLoyalty);
  }

  // ---- public API for themes (ADR D7: the Consumer-SDK seam) ------------------
  // Theme JS may call these; getCustomerToken is the ONLY sanctioned way to get
  // a bearer for a customer-scoped fetch, and the token must never be persisted
  // or sent anywhere but the DOEH consumer API.
  window.DoehIdentity = {
    signIn: function () { return signIn(); },
    signOut: signOut,
    isSignedIn: isSignedIn,
    getCustomer: getCustomer,
    getCustomerToken: function () { return getAccessToken(false); },
    render: render,
  };

  // ---- boot -----------------------------------------------------------------
  function boot() {
    // The callback page: finish the exchange and leave.
    if (location.pathname.replace(/\/+$/, "") === "/doeh/callback" ||
        document.querySelector('[data-doeh-widget="callback"]')) {
      handleCallback();
      return;
    }
    expandShortcodes();
    render();
    announce();
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
