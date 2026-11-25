#!/bin/sh
set -e
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
BROWSER="${BROWSER:-/Applications/Brave Browser.app/Contents/MacOS/Brave Browser}"
[ -x "$BROWSER" ] || BROWSER="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

"$BROWSER" --headless=new --disable-gpu --allow-file-access-from-files --no-pdf-header-footer \
    --virtual-time-budget=4000 --print-to-pdf="$ROOT/assets/cv/loris-populaire-cv.pdf" \
    "file://$ROOT/resources/cv/cv.html"

echo "CV généré : assets/cv/loris-populaire-cv.pdf"
