<?php
/**
 * LUMIZERN — Ultra‑Optimized Header
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="lz-header" role="banner">
  <div class="lz-header-inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="lz-logo" aria-label="<?php bloginfo('name'); ?> home">
      <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/logo.svg'); ?>" alt="<?php bloginfo('name'); ?>" width="120" height="40" loading="eager" />
    </a>

    <nav class="lz-nav" aria-label="Primary Menu">
      <?php
      wp_nav_menu([
        'theme_location' => 'primary',
        'container' => false,
        'menu_class' => 'lz-menu',
        'fallback_cb' => false,
        'depth' => 2,
      ]);
      ?>
    </nav>

    <div class="lz-icons">
      <button class="lz-icon-btn lz-search-trigger" data-lz-search-open aria-label="<?php esc_attr_e('Search', 'lumizern-vibe'); ?>">
        <span aria-hidden="true">⌕</span>
      </button>
      <?php if (function_exists('wc_get_page_id')) : ?>
      <a href="<?php echo esc_url(function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('vibe-profile') : home_url('/vibe-profile/')); ?>" class="lz-icon-btn" aria-label="<?php esc_attr_e('My vibe profile', 'lumizern-vibe'); ?>">✨</a>
      <a href="<?php echo esc_url(get_permalink(wc_get_page_id('myaccount'))); ?>" class="lz-icon-btn" aria-label="<?php esc_attr_e('Account', 'lumizern-vibe'); ?>">👤</a>
      <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="lz-icon-btn lz-cart" aria-label="<?php esc_attr_e('Cart', 'lumizern-vibe'); ?>">🛒
        <span class="lz-cart-count"><?php echo function_exists('WC') && WC()->cart ? esc_html(WC()->cart->get_cart_contents_count()) : '0'; ?></span>
      </a>
      <?php endif; ?>
      <button class="lz-icon-btn lz-mobile-toggle" data-lz-mobile-toggle aria-label="<?php esc_attr_e('Menu', 'lumizern-vibe'); ?>" aria-expanded="false">☰</button>
    </div>
  </div>

  <div class="lz-mobile-nav" aria-hidden="true">
    <?php
    wp_nav_menu([
      'theme_location' => 'primary',
      'container' => false,
      'menu_class' => 'lz-mobile-menu',
      'fallback_cb' => false,
    ]);
    ?>
  </div>

  <div class="lz-search-overlay" aria-hidden="true">
    <form role="search" method="get" class="lz-search-box" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="search" name="s" placeholder="<?php esc_attr_e('Search products...', 'lumizern-vibe'); ?>" autocomplete="off">
      <button type="submit" aria-label="<?php esc_attr_e('Search', 'lumizern-vibe'); ?>">⌕</button>
    </form>
    <button class="lz-search-close" data-lz-search-close aria-label="<?php esc_attr_e('Close', 'lumizern-vibe'); ?>">✕</button>
  </div>
</header>
