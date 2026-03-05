# Lumizern Theme Assets (2026 Foundation)

This folder contains production-ready CSS/JS baseline assets to drop into your WordPress theme enqueue process.

## Files
- `assets/css/lumizern-2026.css`
  - Fixes CSS errors from prior draft (invalid variable syntax, missing variables, selector collisions).
  - Adds consistent styling for Woo cart/checkout/account.
  - Keeps affiliate badge UX and improved product cards.
- `assets/js/lumizern-2026.js`
  - Handles sticky header state, mobile nav toggle, search overlay toggle, and cart notification helper.

## Enqueue (functions.php)

```php
wp_enqueue_style(
    'lumizern-2026',
    get_template_directory_uri() . '/theme/assets/css/lumizern-2026.css',
    array(),
    '3.1.0'
);

wp_enqueue_script(
    'lumizern-2026',
    get_template_directory_uri() . '/theme/assets/js/lumizern-2026.js',
    array(),
    '3.1.0',
    true
);
```

## Markup hooks expected by JS
- Mobile toggle button: `data-lz-mobile-toggle`
- Search open button: `data-lz-search-open`
- Search close button: `data-lz-search-close`
- Containers: `.lz-mobile-nav`, `.lz-search-overlay`, `.lz-header`
