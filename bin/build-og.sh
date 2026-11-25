#!/bin/sh
set -e
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BROWSER="${BROWSER:-/Applications/Brave Browser.app/Contents/MacOS/Brave Browser}"
[ -x "$BROWSER" ] || BROWSER="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
TMP="$(mktemp -d)"

"$BROWSER" --headless=new --disable-gpu --hide-scrollbars --allow-file-access-from-files \
    --window-size=1200,630 --virtual-time-budget=3000 --screenshot="$TMP/og.png" \
    "file://$ROOT/resources/og/og-image.html" >/dev/null 2>&1
sips -s format jpeg -s formatOptions 85 "$TMP/og.png" --out "$ROOT/assets/img/og-image.jpg" >/dev/null
rm -rf "$TMP"

echo "Image de partage générée : assets/img/og-image.jpg"
