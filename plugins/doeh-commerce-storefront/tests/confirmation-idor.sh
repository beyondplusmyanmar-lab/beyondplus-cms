#!/usr/bin/env bash
#
# Storefront guest-confirmation IDOR acceptance — the security baseline for the
# session-bound order confirmation (0.1.2). Asserts that the /store/order/{id}
# page reveals order details only to the session that placed the order, and is
# not an oracle for which order ids exist.
#
# It exercises the CMS SESSION layer (cookie jars), which the wire-level
# golden-client-commerce.php in doeh-pos-web cannot reach — that one proves the
# Orders API contract; this one proves object-level authorization in the
# storefront presentation layer.
#
# Prerequisites: the CMS running (default http://127.0.0.1:8890) with
# doeh-commerce-storefront active and doeh-commerce configured with a sandbox
# sk_ that can price the SKU below.
#
# Run:  BASE=http://127.0.0.1:8890 SKU=D-001 bash tests/confirmation-idor.sh
set -u
BASE="${BASE:-http://127.0.0.1:8890}"
SKU="${SKU:-D-001}"
A=$(mktemp); B=$(mktemp)   # two cookie jars = two independent browser sessions
pass=0; fail=0
ok(){ if [ "$2" = "1" ]; then echo "  ok   $1"; pass=$((pass+1)); else echo "  FAIL $1"; fail=$((fail+1)); fi; }
tok(){ grep -oP 'name="_token"\s+value="\K[^"]+' | head -1; }
code(){ curl -s -o /dev/null -w '%{http_code}' -b "$1" -c "$1" "$BASE$2"; }
bodyhas(){ curl -s -b "$1" -c "$1" "$BASE$2" | grep -qE "$3" && echo 1 || echo 0; }

echo "— session A places an order —"
t=$(curl -s -c "$A" -b "$A" "$BASE/store" | tok)
curl -s -c "$A" -b "$A" -o /dev/null -X POST "$BASE/store/cart/add" --data-urlencode "_token=$t" --data-urlencode "sku=$SKU"
t=$(curl -s -c "$A" -b "$A" "$BASE/store/cart" | tok)
LOC=$(curl -s -c "$A" -b "$A" -o /dev/null -D - -X POST "$BASE/store/checkout" --data-urlencode "_token=$t" | grep -i '^location:' | tr -d '\r' | awk '{print $2}')
OID=$(echo "$LOC" | grep -oE 'ord_[A-Za-z0-9]+')
ok "checkout created an order ($OID)" "$([ -n "$OID" ] && echo 1 || echo 0)"
PREV="ord_$(( ${OID#ord_} - 1 ))"   # the immediately-previous (someone else's) id

echo "— 1. the placing session sees its OWN order in full —"
ok "session A sees order details" "$(bodyhas "$A" "/store/order/$OID" '[0-9],[0-9]{3}\s+[A-Z]{3}')"

echo "— 2. a different session CANNOT see it (IDOR blocked) —"
ok "session B sees NO details for $OID" "$([ "$(bodyhas "$B" "/store/order/$OID" '[0-9],[0-9]{3}\s+[A-Z]{3}')" = 0 ] && echo 1 || echo 0)"

echo "— 3. sequential id enumeration by the same session leaks nothing —"
ok "session A sees NO details for the previous id $PREV" "$([ "$(bodyhas "$A" "/store/order/$PREV" '[0-9],[0-9]{3}\s+[A-Z]{3}')" = 0 ] && echo 1 || echo 0)"

echo "— 4. an invalid id returns a generic 200 (never 404) —"
ok "invalid id → HTTP 200" "$([ "$(code "$B" /store/order/ord_9999999)" = 200 ] && echo 1 || echo 0)"

echo "— 5. real-but-not-owned is INDISTINGUISHABLE from invalid (no oracle) —"
R1=$(curl -s -b "$B" -c "$B" "$BASE/store/order/$PREV")
R2=$(curl -s -b "$B" -c "$B" "$BASE/store/order/ord_9999999")
ok "the two responses are byte-identical" "$([ "$R1" = "$R2" ] && echo 1 || echo 0)"

echo "— 6. every response is 200, never 404 —"
ok "own=200 other=200 invalid=200" "$([ "$(code "$A" /store/order/$OID)" = 200 ] && [ "$(code "$B" /store/order/$OID)" = 200 ] && [ "$(code "$B" /store/order/ord_9999999)" = 200 ] && echo 1 || echo 0)"

rm -f "$A" "$B"
echo; echo "$([ $fail = 0 ] && echo ALL GREEN || echo FAILURES:$fail) — passed $pass, failed $fail"
exit $([ $fail = 0 ] && echo 0 || echo 1)
