# First Order Walkthrough — go-live day

The merchant-facing version of the runbook's acceptance walk (§8). This is the
moment the platform's whole proof chain lands with a real business: one
customer, one order, one merchant operating it. Treat it as a small ceremony —
it is also your production acceptance test.

**Setup:** the storefront is live on the merchant's domain, the wizard is
complete, the Orders dashboard loads. The merchant is present and drives their
own side. The "customer" uses a real phone on a public network — not the dev
machine, not the shop Wi-Fi if avoidable.

---

## The customer's five steps

```
Phone
  → open the storefront (the merchant's own domain)
  → browse, add items to cart
  → checkout: choose fulfilment, enter name + phone
  → place order
  → confirmation screen
```

Narrate nothing. Let the merchant watch a stranger-shaped flow happen on a
phone, on their site, with their products and prices.

## The merchant's three steps

```
Dashboard (Admin → Commerce → Orders)
  → the order appears in today's list
  → open it: items, amount, customer reference, fulfilment, time
  → merchant acts on it (their call — this is the point)
```

Then ask the one scripted question, and write the answer verbatim:

> **"What would you do next with this order?"**

Their unprompted answer tells you whether the flow matches how they actually
run the business — it is acceptance evidence, not small talk.

## What to record (from the runbook's evidence table)

| Question | Pass looks like |
|---|---|
| Can they operate without developer help? | They drove the dashboard after one walkthrough |
| Is the theme understandable? | They did branding/content edits alone earlier |
| Does the order flow match their reality? | Their step-3 answer |
| What's missing? | Their asks, **verbatim** — filed, not promised |

## Handling what they ask for (on the spot)

Never say yes to a feature during the walk. Classify later, per the runbook:

- **Broken?** → bug fix (allowed under the freeze).
- **Missing step in the existing order workflow?** → candidate for a minor.
- **New territory** (payments, delivery management, booking, inventory)?
  → "Good ask — noted. That becomes its own project when merchants need it."
- **Look/behaviour specific to them?** → theme/plugin layer, quoted work.

## If something fails during the walk

- Checkout fails → check the key status and the Orders API from the admin
  side; the wizard's step 4 can re-prove the key. Nothing customer-entered is
  lost by retrying.
- Dashboard empty though the order confirmed → confirm you're looking at
  today's window (the list is a bounded window report); search by order id.
- Total outage → the instance can be rebuilt from the runbook §2 without
  losing the merchant's platform data; don't debug live in front of them —
  reschedule the walk. A failed first impression costs more than a day's
  delay.

When the table is filled, the merchant is not a demo audience anymore — they
are the platform's first production acceptance customer, and their missing
feature list is the only roadmap input that counts.
