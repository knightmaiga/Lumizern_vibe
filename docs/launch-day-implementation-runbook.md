# Lumizern Launch-Day Implementation Runbook (Full Step-by-Step)

This runbook is for executing all high-impact launch fixes in one day with minimal risk.

## 0) Freeze + backup (30 min)
1. Create a release branch from current production-ready commit.
2. Take full DB + files backup.
3. Confirm rollback commit/tag exists.

## 1) Design system unification (60–90 min)
1. Use `theme/style.css` as orchestration layer.
2. Keep shared tokens (`--lz-*`) only once.
3. Ensure CTA classes are global:
   - `.lz-btn`
   - `.lz-btn-ghost`
4. Ensure focus-visible rules exist for links/buttons/inputs.

## 2) Remove inline visual debt (30 min)
1. Remove inline `<style>` from quiz template (`page-vibe-quiz.php`).
2. Move equivalent selectors into global stylesheet.
3. Re-test quiz navigation and result rendering.

## 3) CTA hierarchy standardization (60 min)
1. Above the fold on each template: keep one primary CTA.
2. Convert utility links that drive conversion/profile action into button-like links.
3. Ensure profile/action links in shop + vibe pages are visually obvious.

## 4) Cart + checkout premium alignment (60 min)
1. Apply global button classes to cart and checkout CTAs.
2. Keep form fields visually aligned with global system.
3. Verify place-order button is dominant and accessible.

## 5) Personalization + monetization verification (45 min)
1. Confirm trend block renders with active vibe.
2. Confirm profile save updates cookie/user meta.
3. Confirm newsletter form requires consent and stores lead metadata.

## 6) Security/compliance verification (45 min)
1. Verify all AJAX endpoints use nonce checks.
2. Verify affiliate outbound links contain `nofollow sponsored` + `_blank`.
3. Confirm legal pages exist and are linked in footer/nav.

## 7) Production checks (30 min)
Run from repo root:

```bash
bash scripts/preflight-production.sh
```

Then run manual smoke checks:
- Home
- Shop archive
- Vibe taxonomy
- Product page
- Quiz page
- Profile page
- Cart
- Checkout

## 8) Launch cutover (30 min)
1. Deploy release branch.
2. Purge CDN cache except dynamic Woo pages.
3. Verify checkout with one real transaction.
4. Watch logs/analytics for 2 hours post-launch.

## 9) 24-hour post-launch watch
Track:
- Quiz completion rate
- Affiliate outbound CTR
- Add-to-cart rate
- Checkout completion
- Error logs (PHP/Woo)

If any metric or errors drop sharply, rollback immediately and patch in staging.
