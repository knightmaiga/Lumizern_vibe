# Lumizern 2026 Design Sprint Plan (Implemented + Next)

This sprint plan aligns the full storefront to a premium, cohesive 2026 visual + conversion system.

## Sprint goals

1. Unify interaction hierarchy (all key CTAs are explicit button-style actions).
2. Standardize visual tokens across home, shop, vibe pages, profile, cart, checkout.
3. Reduce cognitive noise while increasing perceived quality.
4. Keep performance and accessibility intact.

## Sprint 1 (Implemented now)

- Converted profile-context inline text links into clear button CTAs:
  - Shop archive: **Change profile** button
  - Vibe taxonomy: **Update profile** button
- Introduced shared premium CTA classes:
  - `.lz-btn`
  - `.lz-btn-ghost`
- Added elevated 2026 surfaces and spacing rhythm:
  - section spacing utility
  - glass card treatment
  - stronger focus/hover states

## Sprint 2 (Next 1 week)

- Move remaining inline quiz CSS into global stylesheets.
- Normalize heading scale and spacing rhythm on every template.
- Add consistent “primary action first” layout rule per section.
- Tighten cart/checkout visual alignment with global card + form tokens.

## Sprint 3 (Next 2–3 weeks)

- Add skeleton loading states to product grids.
- Add motion preference-aware microinteractions for CTA and cards.
- Improve component-level accessibility contrast checks.
- Expand profile-aware merchandising blocks (home + vibe + PDP).

## Release acceptance criteria

- Every conversion-critical template has one dominant CTA above fold.
- CTA links appear as accessible button-like actions (visual + focus states).
- No visual regressions on mobile breakpoints.
- Preflight + lint checks pass before deployment.
