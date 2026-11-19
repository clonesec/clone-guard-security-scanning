#!/bin/sh
# Turn the local demo portal on or off on the main wp-env site (port 8888).
# On: every CloneGuard portal call is answered with invented data and nothing reaches the real portal.
# Use it for WordPress.org screenshots, so no customer data appears. Usage: scripts/demo-portal.sh on|off
set -e
case "$1" in
  on)
    npx wp-env run cli sh -c 'mkdir -p wp-content/mu-plugins && cp wp-content/plugins/clone-guard-security-scanning/scripts/demo-portal.php wp-content/mu-plugins/cgss-demo-portal.php'
    ;;
  off)
    npx wp-env run cli sh -c 'rm -f wp-content/mu-plugins/cgss-demo-portal.php'
    ;;
  *)
    echo "Usage: $0 on|off" >&2; exit 1 ;;
esac
# Cached portal data (scan configs, scanners, Overview summary) would otherwise mix real and demo data.
npx wp-env run cli wp transient delete --all >/dev/null
echo "Demo portal: $1"
