#!/usr/bin/env bash
# Read-only probe of the CloneGuard portal API v1 (Plan step 4).
#
# Calls every GET endpoint the plugin uses and prints only the HTTP status, the time taken and the
# top-level JSON keys. It never prints the token, the API key, IDs or response bodies (bodies can
# contain customer data). It never calls create, update, delete, start, stop or generate endpoints
# (note: /reports/{id}/generate is a GET but starts report generation, so it is not called).
#
# Usage: scripts/api-probe.sh            (reads CGSS_PORTAL_URL, CGSS_USER_TOKEN, CGSS_API_KEY from .env)
# Needs: bash, curl, python3.

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$ROOT/.env"
[ -f "$ENV_FILE" ] || { echo "Missing $ENV_FILE (copy .env.example and fill it in)." >&2; exit 1; }

val() { awk -v k="$1" 'index($0, k "=") == 1 { print substr($0, length(k) + 2) }' "$ENV_FILE" | tr -d '\r'; }
PORTAL="$(val CGSS_PORTAL_URL)"; PORTAL="${PORTAL:-pciscan.clone-systems.com}"
[ -n "$(val CGSS_USER_TOKEN)" ] && [ -n "$(val CGSS_API_KEY)" ] || { echo "CGSS_USER_TOKEN and CGSS_API_KEY must be set in .env." >&2; exit 1; }
BASE="https://$PORTAL/API/v1"

TMP="$(mktemp -d)"; trap 'rm -rf "$TMP"' EXIT
umask 077
# Headers go through a file so the secrets never appear on a command line.
printf 'Authorization: Basic %s\nx-api-key: %s\nUser-Agent: CloneGuardSecurityScanning-api-probe\n' "$(val CGSS_USER_TOKEN)" "$(val CGSS_API_KEY)" > "$TMP/headers"

# get <label> <path>: GET, save body to $TMP/body, print status, time and top-level keys.
get() {
    local label="$1" path="$2" out
    out="$(curl -s -o "$TMP/body" -w '%{http_code} %{time_total}' --max-time 90 -H @"$TMP/headers" "$BASE$path" || echo "000 0")"
    printf '%-34s %s %6.1fs  keys=%s\n' "$label" "${out% *}" "${out#* }" "$(python3 -I -c '
import json, sys
try:
    d = json.load(open(sys.argv[1]))
    print("[" + ",".join(d.keys()) + "]" if isinstance(d, dict) else type(d).__name__)
except Exception:
    print("non-JSON")' "$TMP/body")"
}

# first_id <collection> [require]: first item id in the last body; with "exception", the first result's overrides id.
first_id() {
    python3 -I -c '
import json, sys
col, mode = sys.argv[2], (sys.argv[3] if len(sys.argv) > 3 else "")
def items(x):
    if isinstance(x, dict):
        if "id" in x: yield x
        else:
            for v in x.values(): yield from items(v)
    elif isinstance(x, list):
        for v in x: yield from items(v)
try:
    d = json.load(open(sys.argv[1]))
    for it in items(d.get(col)):
        if mode == "exception":
            o = it.get("overrides")
            if isinstance(o, dict) and o.get("id"): print(o["id"]); break
        else:
            print(it["id"]); break
except Exception:
    pass' "$TMP/body" "$@"
}

# report_with_file: first report id and file type with status "done", from the last body.
report_with_file() {
    python3 -I -c '
import json, sys
try:
    d = json.load(open(sys.argv[1])); r = d.get("reports") or []
    items = r[0] if isinstance(r, list) and r else r
    items = list(items.values()) if isinstance(items, dict) else items
    for it in items:
        f = it.get("files") or {}
        for k, v in (f.items() if isinstance(f, dict) else []):
            if isinstance(v, dict) and v.get("status") == "done": print(it["id"], k); sys.exit()
except Exception:
    pass' "$TMP/body"
}

echo "Portal: $PORTAL  (GET only; status, time, top-level keys)"
get "my_details"                    "/my_details"

get "scans (list)"                  "/scans?page=1"
id="$(first_id scans)";            [ -n "$id" ] && get "scans/{id}" "/scans/$id" || echo "scans/{id}                         skipped (no scan)"

get "reports (list)"                "/reports?page=1&per_page=10"
id="$(first_id reports)"
if [ -n "$id" ]; then get "reports/{id}" "/reports/$id"; get "reports/{id}/details" "/reports/$id/details"; else echo "reports/{id}                       skipped (no report)"; fi
get "reports (list, 50, for files)" "/reports?page=1&per_page=50"
read -r rid rtype <<< "$(report_with_file || true)" || true
if [ -n "${rid:-}" ]; then
    out="$(curl -s -o /dev/null -w '%{http_code} %{time_total} %{size_download} %{content_type}' --max-time 90 -H @"$TMP/headers" "$BASE/reports/$rid.pdf?type=$rtype" || echo "000 0 0 -")"
    printf '%-34s %s %6.1fs  size=%sB type=%s\n' "reports/{id}.pdf?type=$rtype" $(echo "$out" | cut -d' ' -f1-2) "$(echo "$out" | cut -d' ' -f3)" "$(echo "$out" | cut -d' ' -f4-)"
else
    echo "reports/{id}.pdf                   skipped (no generated file in the first 50 reports)"
fi

get "results (list)"                "/results?page=1&per_page=10"
cp "$TMP/body" "$TMP/results"
id="$(first_id results)";          [ -n "$id" ] && get "results/{id}" "/results/$id" || echo "results/{id}                       skipped (no result)"
cp "$TMP/results" "$TMP/body"; id="$(first_id results exception)"
[ -n "$id" ] && get "exceptions/{id}" "/exceptions/$id" || echo "exceptions/{id}                    skipped (no exception on results page 1)"

for col in schedules targets notifications; do
    get "$col (list)" "/$col?page=1"
    id="$(first_id "$col")"; [ -n "$id" ] && get "$col/{id}" "/$col/$id" || echo "$col/{id}  skipped (none)"
done

get "scanners/activated"            "/scanners/activated"
get "configs"                       "/configs?active=false&per_page=100"
