#!/bin/bash
set -euo pipefail

# Build frontend assets so pages that depend on Vite/Tailwind are styled in production.
if [ -f package.json ]; then
  npm ci
  npm run build
fi
