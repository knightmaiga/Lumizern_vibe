#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "[1/6] PHP syntax checks"
php -l theme/functions.php >/dev/null
php -l theme/front-page.php >/dev/null
php -l theme/footer.php >/dev/null
php -l theme/header.php >/dev/null
php -l theme/archive-product.php >/dev/null
php -l theme/taxonomy-vibe.php >/dev/null
php -l theme/single-product.php >/dev/null
php -l theme/page-vibe-quiz.php >/dev/null
php -l theme/page-vibe-profile.php >/dev/null
php -l theme/woocommerce/cart/cart.php >/dev/null
php -l theme/woocommerce/checkout/form-checkout.php >/dev/null

echo "[2/6] JavaScript syntax check"
node --check theme/assets/js/lumizern-2026.js >/dev/null

echo "[3/6] Required files present"
required_files=(
  "README.md"
  "docs/launch-checklist.md"
  "docs/seo-discoverability-plan.md"
  "docs/affiliate-dropship-ops.md"
  "docs/production-readiness-checklist.md"
  "theme/style.css"
  "theme/assets/css/lumizern-2026.css"
  "theme/assets/js/lumizern-2026.js"
)
for f in "${required_files[@]}"; do
  [[ -f "$f" ]] || { echo "Missing required file: $f"; exit 1; }
done

echo "[4/6] Critical routes/templates referenced"
rg -q "Template Name: Vibe Quiz" theme/page-vibe-quiz.php
rg -q "Template Name: Vibe Profile" theme/page-vibe-profile.php
rg -q "add_rewrite_endpoint\('vibe-profile'" theme/functions.php

echo "[5/6] Security-related nonces present"
rg -q "check_ajax_referer\('lumizern_2025_nonce'" theme/functions.php
rg -q "check_ajax_referer\('lumizern_quiz_profile_nonce'" theme/functions.php

echo "[6/6] Affiliate safety attributes present"
rg -q "nofollow sponsored" theme/functions.php theme/archive-product.php theme/taxonomy-vibe.php

echo "✅ Production preflight checks passed"
