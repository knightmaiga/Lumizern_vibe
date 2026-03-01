# Launch Checklist (Lumizern Vibe)

## A. Infrastructure
- [ ] Buy domain (compare first-year + renewal cost).
- [ ] Point DNS to Cloudflare.
- [ ] Enable SSL (Full Strict).
- [ ] Create staging site.
- [ ] Configure daily backup + weekly offsite backup.
- [ ] Setup uptime monitor (free: UptimeRobot).

## B. WordPress + WooCommerce
- [ ] Install WordPress latest stable.
- [ ] Install WooCommerce and complete setup wizard.
- [ ] Configure currency, tax, shipping zones, returns.
- [ ] Enable payment gateways: Stripe + PayPal.
- [ ] Configure SMTP provider for transactional emails.
- [ ] Enable image optimization + caching plugin.

## C. Theme & Vibe Engine
- [ ] Install Lumizern theme + child theme.
- [ ] Register/verify `vibe` taxonomy on `product`, `post`, `affiliate_post`.
- [ ] Ensure all 6 vibe terms exist with exact slugs.
- [ ] Verify taxonomy route `/vibe/{slug}/`.
- [ ] Test `taxonomy-vibe.php` rendering: hero, products, guides, benefits.
- [ ] Verify quiz page template assignment.
- [ ] Fix quiz result links to canonical slugs.

## D. Product Catalog
- [ ] Import products with `data/vibe-product-intake-template.csv`.
- [ ] Add product images (consistent aspect ratio).
- [ ] Add short descriptions and benefit bullets.
- [ ] Assign at least 1 vibe per product.
- [ ] Mark affiliate products with `_is_affiliate=yes` and `_affiliate_url`.

## E. Conversion & Trust
- [ ] Add shipping, return, and delivery promises near CTAs.
- [ ] Add social proof sections (reviews/UGC/testimonials).
- [ ] Add FAQ blocks on product + vibe pages.
- [ ] Add urgency copy carefully (ethical, truthful).

## F. Analytics & SEO
- [ ] GA4 + Search Console connected.
- [ ] XML sitemap submitted.
- [ ] Robots and canonical tags verified.
- [ ] Product schema + article schema enabled.
- [ ] Track quiz and checkout funnel events.

## G. QA Before Launch
- [ ] Test mobile nav, filters, product pages.
- [ ] Test add-to-cart for normal products.
- [ ] Test affiliate outbound flow and rel attributes.
- [ ] Test checkout from cart to confirmation.
- [ ] Test account registration/login/password reset.
- [ ] Test 404, 500 fallback pages.

