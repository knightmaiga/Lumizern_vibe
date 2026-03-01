# Lumizern Vibe Production Readiness Checklist

Use this checklist before every production release. Goal: protect conversion, trust, and operational stability.

## 1) Core platform baseline

- [ ] WordPress, WooCommerce, theme, and plugins updated in staging first.
- [ ] PHP 8.2+ and MySQL/MariaDB versions meet hosting recommendations.
- [ ] HTTPS forced sitewide.
- [ ] Daily backups verified (files + DB + restore test).

## 2) Environment and deployment

- [ ] Staging URL separate from production.
- [ ] `WP_DEBUG` disabled on production (`false`).
- [ ] File editing disabled in WP admin (`DISALLOW_FILE_EDIT` true).
- [ ] Deploy from version control only (no direct production edits).
- [ ] Rollback plan prepared (previous release tag/commit).

## 3) WooCommerce transaction-critical checks

- [ ] Cart flow tested on desktop and mobile.
- [ ] Checkout flow tested with at least one successful transaction.
- [ ] Tax/shipping rates validated for target regions.
- [ ] Refund flow tested once.
- [ ] Dynamic routes excluded from full-page cache:
  - [ ] `/cart/`
  - [ ] `/checkout/`
  - [ ] `/my-account/`
  - [ ] `wc-ajax` endpoints

## 4) Lumizern-specific product model checks

- [ ] Vibe taxonomy terms exist and resolve correctly.
- [ ] Quiz result slugs map to valid vibe archive URLs.
- [ ] Affiliate products have valid `_affiliate_url` and `_is_affiliate=yes`.
- [ ] Affiliate links open with `target="_blank" rel="nofollow sponsored"`.
- [ ] Fulfillment labels are clear and accurate (affiliate/partner/in-house).
- [ ] Trend Lab tool (Tools → Vibe Trend Lab) returns products per vibe.

## 5) Personalization + lead capture checks

- [ ] Vibe profile endpoint `/my-account/vibe-profile/` loads for logged-in users.
- [ ] Quiz profile save endpoint returns success with valid nonce.
- [ ] Newsletter form blocks submission without consent.
- [ ] Newsletter AJAX endpoint stores leads and source metadata.
- [ ] SMTP configured and test email delivered to inbox (not spam).

## 6) Security hardening

- [ ] Cloudflare WAF and bot protections enabled.
- [ ] XML-RPC disabled unless explicitly required.
- [ ] Login brute-force protection/rate limiting enabled.
- [ ] Admin accounts use strong passwords + MFA.
- [ ] Unused plugins/themes removed.
- [ ] Principle of least privilege applied to admin/editor/shop-manager roles.

## 7) SEO + content quality checks

- [ ] Indexing enabled only on production (not staging).
- [ ] XML sitemap submitted in Search Console.
- [ ] Canonical URLs verified for product and vibe pages.
- [ ] Structured data plugin configured (Product + Article + Breadcrumb).
- [ ] At least 3 high-intent guides queued post-launch.
- [ ] Internal links from guides → vibe pages → products validated.

## 8) Performance and CWV checks

- [ ] LCP image optimized (WebP/AVIF where possible).
- [ ] Hero assets preloaded only when justified.
- [ ] JavaScript execution audited for unnecessary blocking.
- [ ] CDN cache rules verified.
- [ ] Core Web Vitals checked on key templates:
  - [ ] Home
  - [ ] Shop archive
  - [ ] Vibe taxonomy
  - [ ] Single product
  - [ ] Cart
  - [ ] Checkout

## 9) Trust, legal, and compliance

- [ ] Privacy Policy published.
- [ ] Terms of Service published.
- [ ] Shipping Policy published.
- [ ] Returns/Refund Policy published.
- [ ] Affiliate Disclosure published and linked.
- [ ] Contact page published and accessible.

## 10) Revenue operations

- [ ] Top 3 vibe collections curated manually (no low-quality filler products).
- [ ] At least one affiliate guide per core vibe published.
- [ ] Email welcome sequence enabled (profile-aware if possible).
- [ ] Basic KPI dashboard configured:
  - [ ] Sessions
  - [ ] Quiz completion rate
  - [ ] Affiliate CTR
  - [ ] Add-to-cart rate
  - [ ] Checkout conversion rate
  - [ ] Revenue by vibe

## 11) Post-launch 72-hour watch

- [ ] Monitor server errors and PHP logs.
- [ ] Monitor checkout abandonment and payment errors.
- [ ] Monitor broken links and 404s.
- [ ] Monitor email deliverability and unsubscribe rate.
- [ ] Fix highest-impact issue first (revenue > polish).

---

## Fast command checks (repo level)

Run this from repo root:

```bash
bash scripts/preflight-production.sh
```

This command validates key syntax/static checks and confirms critical files are present.
