# Lumizern Vibe — WordPress/WooCommerce Implementation Guide (2026)

Lumizern Vibe is a **vibe-first WooCommerce storefront** with a hybrid catalog model:
- normal products (cart + checkout)
- affiliate products (outbound click)
- optional partner/dropship products

This repository contains a working theme scaffold plus strategy docs for conversion, SEO, and operations.

## 1) Requirements

- WordPress 6.6+
- PHP 8.2+
- WooCommerce 9+
- HTTPS enabled
- MySQL/MariaDB supported by host

## 2) Installation

1. Copy this repo theme files into your theme directory.
2. Activate theme in WordPress admin.
3. Install/activate WooCommerce.
4. Visit **Settings → Permalinks** and save once.
5. Confirm routes:
   - `/shop/`
   - `/vibe/{slug}/`
   - `/affiliate-guides/`

## 3) Recommended Plugins (minimal)

- SEO: Rank Math or Yoast (choose one)
- Caching: LiteSpeed Cache (LiteSpeed server) or WP Rocket equivalent
- Image optimization: ShortPixel / Imagify
- SMTP: FluentSMTP or WP Mail SMTP
- Consent: lightweight CMP compatible with GA4/Meta

Avoid plugin bloat; prefer code-level customization where practical.

## 4) Core Theme Architecture

- `theme/functions.php`
  - `LumizernVibe2025` singleton
  - registers taxonomy/CPT
  - enqueue assets
  - AJAX endpoints `get_vibe_products` and `lumizern_save_quiz_profile`
  - affiliate product behavior hooks
  - vibe profile personalization helpers
- Templates
  - `front-page.php`
  - `taxonomy-vibe.php`
  - `archive-product.php`
  - `single-product.php`
  - `page-vibe-quiz.php`
  - Woo overrides:
    - `woocommerce/cart/cart.php`
    - `woocommerce/checkout/form-checkout.php`

## 5) Vibe Taxonomy and Slugs

Canonical vibe slugs:
- `cozy-cocoon`
- `power-play`
- `aesthetic-curator`
- `zen-chill`
- `creative-hustle`
- `pawfectionist`

Default terms are auto-created once via option `lumizern_default_vibes_created=yes`.

## 6) Quiz Behavior

Template: **Vibe Quiz 2025 - Enhanced** (`theme/page-vibe-quiz.php`)

- 8-step client-side quiz
- Scores across all 6 vibe slugs
- returns primary/secondary/tertiary vibe
- central data currently fully detailed for 2 vibes + fallback mapping for remaining vibes
- score updates now handle answer changes safely (no cumulative double count)

## 7) Affiliate Product Behavior

Meta keys:
- `_is_affiliate` (`yes`/`no`)
- `_affiliate_url` (external URL)

When affiliate is enabled:
- badge shown in loops
- add-to-cart text becomes `View Product →`
- URL changes to affiliate URL
- opens in new tab
- `rel="nofollow sponsored"`
- `_price` cleared by save handler

## 8) AJAX Endpoints

- `get_vibe_products`
  - nonce: `lumizern_2025_nonce`
  - input: `vibe_slug`, `paged`
  - output: products list + pagination metadata
- `lumizern_save_quiz_profile`
  - nonce: `lumizern_quiz_profile_nonce`
  - input: `primary`, `secondary`, `tertiary`, `email_opt_in`
  - output: normalized profile + cookie/user-meta sync

## 9) Deployment (Staging → Production)

1. Deploy to staging first.
2. Validate cart/checkout/account flows.
3. Verify affiliate outbound links + disclosures.
4. Prime cache and exclude dynamic pages:
   - `/cart/`, `/checkout/`, `/my-account/`
   - `wc-ajax` endpoints
5. Promote to production.


## 9A) Vibe Profile Personalization

- New page template: `theme/page-vibe-profile.php` (**Vibe Profile**)
- New account endpoint: `/my-account/vibe-profile/`
- Profile values are persisted through:
  - user meta (`lumizern_vibe_profile`) for logged-in users
  - secure cookie (`user_vibe_result`) for guests
- Personalization is used in:
  - homepage recommendations
  - shop context bar
  - single-product profile match note
  - taxonomy profile reminder module

This keeps discovery consistent while still allowing users to retake the full quiz.

## 9B) Revenue + Trust Upgrades Included

- Fulfillment labels now standardize monetization clarity:
  - `Affiliate pick`
  - `Ships from partner`
  - `Ships from us`
- Added profile-first cross-sell rails to improve session depth.
- Added conversion trust modules (shipping/returns/security language) on PDP flows.
- Affiliate outbound behavior remains compliant (`nofollow sponsored`, new tab).

## 10) Cloudflare (DNS/CDN only)

- Use Cloudflare for DNS, CDN, WAF, TLS.
- Do **not** treat Cloudflare as WordPress origin hosting.
- Cache rules should bypass Woo dynamic pages and session/cart endpoints.

## 11) Legal/Trust Pages Required

- Privacy Policy
- Terms of Service
- Shipping Policy
- Returns/Refund Policy
- Affiliate Disclosure
- Contact

## 12) Operations

- Use `data/vibe-product-intake-template.csv` for product onboarding
- Keep vibe assignment mandatory for each product
- Publish guide content weekly for SEO + affiliate monetization

## 13) Troubleshooting

- **Vibe pages 404**: save permalinks and flush rewrite rules.
- **Cart count not updating**: verify Woo fragments and JS enqueue order.
- **Affiliate link not applied**: check `_is_affiliate=yes` and valid `_affiliate_url`.
- **Quiz result links wrong**: verify central mapping in `page-vibe-quiz.php` and slug list above.

## 14) Reference Docs

- `docs/elite-upgrade-audit.md`
- `docs/launch-checklist.md`
- `docs/seo-discoverability-plan.md`
- `docs/affiliate-dropship-ops.md`
- `docs/production-readiness-checklist.md`
- `docs/design-sprint-plan-2026.md`
- `docs/launch-day-implementation-runbook.md`

Run fast repo preflight:

```bash
bash scripts/preflight-production.sh
```


## 15) 90-Day Monetization Execution Roadmap

### Days 1–30 (Foundation + Authority)
- Lock vibe positioning and remove non-fit products.
- Keep IA clear: Shop / Vibes / Quiz / Guides / About / Contact.
- Publish 3 SEO guides per week (long-tail intent by vibe).
- Launch quiz + profile + newsletter capture.

### Days 31–60 (Traffic + Validation)
- Continue 2–3 SEO posts weekly.
- Launch Pinterest moodboard distribution and short-form social snippets.
- Track quiz completion, outbound affiliate CTR, add-to-cart rates.
- Run conversion iteration on hero clarity, CTA hierarchy, and page speed.

### Days 61–90 (Scale + Revenue)
- Expand vibe collections and “top picks” merchandising.
- Run segmented email automation by vibe profile.
- Use Vibe Trend Lab outputs to refresh catalog weekly.
- Introduce paid promotion only after organic conversion signal is proven.

## 16) Vibe Trend Lab App (Built-in)

- Admin tool path: **Tools → Vibe Trend Lab**
- Purpose: surface popularity-based top products by each vibe for merchandising decisions.
- Front-end helper: `lumizern_render_trending_vibe_block($vibe_slug, $limit)`
- Shortcode: `[lumizern_trending_vibe vibe="cozy-cocoon" limit="4"]`

## 17) Newsletter + Email Capture (Built-in)

- AJAX endpoint: `lumizern_subscribe_newsletter`
- Lead storage: `lumizern_lead` post type (Tools menu)
- Consent-required form included in footer.
- Source + vibe context metadata stored for segmentation.

## 18) Performance + Security Notes

- Keep Woo dynamic pages excluded from full-page cache (`/cart`, `/checkout`, `/my-account`, `wc-ajax`).
- Enforce HTTPS everywhere, enable WAF/rate limiting at edge (Cloudflare DNS/CDN + security only).
- Retain nonce checks for all AJAX actions; sanitize and escape all user inputs/outputs.
- Regularly prune stale products to preserve premium catalog quality and brand trust.
