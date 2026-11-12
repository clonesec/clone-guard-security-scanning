#!/usr/bin/env bash
# Runs Plugin Check on the files that ship to WordPress.org (repo minus .distignore),
# so dev files (.env, docs/, CONTEXT.md, ...) do not show up as errors.
# Usage: scripts/plugin-check-dist.sh [extra wp plugin check args, e.g. --format=csv]
# Needs the main wp-env site running (npm run env:start).
set -euo pipefail

cd "$(dirname "$0")/.."
slug=clone-guard-security-scanning
cli=$(docker ps --format '{{.Names}}' | grep -E "^wp-env-${slug}-[0-9a-f]+-cli-1$" | head -n 1)
if [ -z "$cli" ]; then
    echo "wp-env CLI container not found; run npm run env:start" >&2
    exit 1
fi

build=$(mktemp -d)
trap 'rm -rf "$build"' EXIT
mkdir -p "$build/$slug"
rsync -a --exclude-from=.distignore ./ "$build/$slug/"

docker exec "$cli" rm -rf /tmp/cgss-dist
docker cp "$build" "$cli":/tmp/cgss-dist
docker exec "$cli" wp plugin check "/tmp/cgss-dist/$slug" --slug="$slug" "${@:---format=table}"
