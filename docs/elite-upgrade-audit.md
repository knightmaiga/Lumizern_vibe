# Lumizern Vibe 2026 Audit + Upgrade Blueprint

## A) Executive Summary (what to change first and why)

1. **Fix conversion-critical consistency issues first (Week 1)**
   - Canonicalize quiz output links to actual taxonomy slugs (`/vibe/cozy-cocoon`, `/vibe/power-play` etc).
   - Prevent quiz score inflation on option changes (current implementation can overcount if user reselects).
   - Keep checkout/cart reliability over visual complexity.
2. **Stabilize architecture and data contracts (Week 1–2)**
   - Define one source of truth for vibe entities, colors, emoji, and URLs.
   - Add a server-side quiz result endpoint (or cookie/user-meta persistence) to avoid losing profile state.
3. **Ship UX system + merchandising loops (Week 2–4)**
   - Implement coherent design tokens and reusable components.
   - Add “continue discovery” blocks (related vibe, editor picks, guide + product pairings).
4. **Scale discoverability + monetization (Month 2+)**
   - Schema-rich vibe pages + guide clusters.
   - Hybrid monetization labels and disclosures with clean CTA differentiation.

---

## B) Architecture Diagram (text-based) + Data Model

### System map

```text
[User]
  ├─> Home (/)
  │    ├─> Vibe Grid -> /vibe/{slug}
  │    ├─> Trending Products -> /product/{slug}
  │    └─> Quiz CTA -> /vibe-quiz
  │
  ├─> Quiz (/vibe-quiz)
  │    ├─> 8-step scoring (client)
  │    ├─> Primary/secondary/tertiary vibe
  │    └─> Shop CTA -> /vibe/{slug}
  │
  ├─> Vibe archive (/vibe/{slug})
  │    ├─> Product query (post_type=product, paged)
  │    ├─> Guide query (post + affiliate_post)
  │    └─> Benefit panel + affiliate-aware cards
  │
  ├─> Product page
  │    ├─> standard Woo add-to-cart
  │    └─> affiliate override -> outbound URL
  │
  └─> Cart -> Checkout
       ├─> Woo core order review/payment hooks
       └─> vibe-aware shell UI (optional header from cookie)
```

### Data entities

- **Vibe taxonomy term**
  - slug, name, description, optional color/emoji term meta
- **Product (Woo product)**
  - standard Woo fields + `_is_affiliate`, `_affiliate_url`
- **Guide content**
  - `post` + `affiliate_post` CPT, both mapped to `vibe`
- **Quiz profile (recommended persistent layer)**
  - guest: signed cookie/session
  - logged-in: user meta (`lumizern_primary_vibe`, score map, timestamp)

---

## C) UX/UI Design System spec

### Typography scale (fluid)
- Display: `clamp(2rem, 4vw, 4rem)`
- H1: `clamp(1.8rem, 3.2vw, 3rem)`
- H2: `clamp(1.4rem, 2.4vw, 2.2rem)`
- Body L: `1.125rem`
- Body: `1rem`
- Meta: `0.875rem`

### Spacing scale
- `4, 8, 12, 16, 24, 32, 48, 64, 96`

### Component library
- Header/Nav (desktop + mobile drawer)
- Card primitives (product, guide, collection)
- Badge system (vibe, affiliate, shipping source)
- CTA hierarchy (`primary`, `secondary`, `ghost`)
- Form controls (inputs, selects, quantity)
- Trust modules (shipping, returns, secure payment)
- Result modules (quiz outcome, reasons, recommendations)

---

## D) Page-by-page upgrade plan

1. **Home**
   - Above-fold: one value prop + one primary CTA (quiz) + one secondary CTA (shop vibes)
   - Add curated blocks: “Editor’s Picks by Vibe”, “New This Week”, “Top Guides”
2. **Shop archive**
   - Add filter chips: vibe + price band + shipping source
   - Keep card height consistent; include rating/social proof where available
3. **Single product**
   - Decision support: FAQ, shipping ETA, returns summary, partner/affiliate status
   - Personalized block: “Best paired with your vibe”
4. **Cart**
   - Keep custom layout but ensure update/remove works with Woo nonces and fragments
5. **Checkout**
   - Keep Woo payment hooks untouched; avoid custom JS that breaks gateways
   - Minimize distractions, maximize trust + clarity
6. **Vibe taxonomy page**
   - Keep hero + benefits + products + guides
   - Add intro copy optimized for intent + internal links to top guides
7. **Quiz page**
   - Short, adaptive progression, score-safe updates, persisted results
8. **Blog + affiliate guides**
   - Use structured templates with product blocks and “shop this vibe” CTA

---

## E) Technical plan

### Refactors
- `functions.php`
  - Add centralized vibe registry helper (`slug => {name,color,emoji,url}`)
  - Add optional server-side quiz persistence endpoint (`wp_ajax_lumizern_save_quiz_profile`)
- `page-vibe-quiz.php`
  - Keep 8-step flow
  - Fix score inflation bug when changing answers
  - Align shop URLs to canonical taxonomy slugs
- Templates
  - Avoid inline mega-CSS in templates long-term; move to modular stylesheet layers

### Performance improvements
- Preload only critical hero media
- Use `content-visibility` carefully only on non-critical below-fold sections
- Defer non-essential scripts
- Ensure cart/checkout/account excluded from full-page cache

### Security/hardening
- Keep nonce checks for AJAX
- Continue strict sanitize/escape discipline
- Maintain outbound `rel="nofollow sponsored"` for affiliate links

---

## F) SEO plan

1. **Schema**
   - Product schema on product pages
   - Article schema on guides
   - Breadcrumb schema globally
2. **Internal linking**
   - Every guide links to 1 vibe page + 2 product pages
   - Every vibe page links to 3–6 guides and top products
3. **Metadata strategy**
   - Distinct title/meta for each vibe archive
   - Avoid thin/duplicate pages; canonicalize parameter pages
4. **20-topic content cluster (starter)**
   - Best cozy bedroom products for better sleep
   - Best power-play desk setup accessories
   - Minimalist aesthetic curator gift guide
   - Zen chill morning routine essentials
   - Creative hustle home studio checklist
   - Pawfectionist apartment setup guide
   - …(expand to 20 in editorial calendar)

---

## G) Monetization plan

- Split labels by fulfillment model:
  - **Ships from us** (normal cart)
  - **Ships from partner** (dropship)
  - **Affiliate pick** (outbound)
- Place affiliate CTAs in guide context first (not aggressive on all product cards)
- Add ethical lead capture on quiz result (“Email my vibe profile + picks”)
- Automate post-quiz sequence:
  - D0 profile result
  - D2 top picks by vibe
  - D5 guide roundup

---

## H) Implementation checklist

### Phase 1 (stability)
- [x] Add cart and checkout template overrides
- [x] Add quiz page template with 8-step flow
- [x] Fix quiz URL mismatch for central data links
- [x] Fix quiz score double-counting when answers are changed

### Phase 2 (conversion)
- [ ] Add unified badges for fulfillment model
- [ ] Add vibe-personalized recommendation blocks on product/cart
- [ ] Add post-quiz persistence endpoint and cookie/user-meta sync

### Phase 3 (SEO + scale)
- [ ] Add schema and metadata automation for vibe archives
- [ ] Publish first 20 guide pages mapped to vibe clusters
- [ ] Add analytics funnel dashboards for quiz -> product -> purchase
