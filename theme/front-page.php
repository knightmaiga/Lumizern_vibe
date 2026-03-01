<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$vibes_data = get_transient('lumizern_homepage_vibes_data');
if ($vibes_data === false) {
    $vibes_data = ['icons' => ['✨', '🛋️', '🎨', '🐾', '💼', '🧘']];
    set_transient('lumizern_homepage_vibes_data', $vibes_data, 6 * HOUR_IN_SECONDS);
}

$user_vibe = isset($_COOKIE['user_vibe_result']) ? sanitize_text_field(wp_unslash($_COOKIE['user_vibe_result'])) : '';
?>
<main class="homepage-main" role="main">
  <section id="vibes" class="vibe-section" aria-labelledby="vibe-section-title">
    <div class="container-vibe">
      <header class="section-header-vibe">
        <h2 id="vibe-section-title" class="section-title-vibe"><?php esc_html_e('Choose Your Vibe', 'lumizern-vibe'); ?></h2>
        <p class="section-subtitle-vibe"><?php esc_html_e('Curated discovery by mood, energy, and lifestyle.', 'lumizern-vibe'); ?></p>
      </header>

      <div class="vibe-grid">
      <?php
      $vibes = get_terms(['taxonomy' => 'vibe', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC']);
      if (!empty($vibes) && !is_wp_error($vibes)) :
          foreach ($vibes as $index => $vibe) :
              $img_url = LumizernVibe2025::getVibeImageUrl($vibe->slug);
              $icon = $vibes_data['icons'][$index % count($vibes_data['icons'])] ?? '✨';
              ?>
              <a href="<?php echo esc_url(get_term_link($vibe)); ?>" class="vibe-card" data-vibe="<?php echo esc_attr($vibe->slug); ?>">
                <div class="card-image-container-vibe"><img class="vibe-image" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($vibe->name); ?>" loading="lazy" /></div>
                <div class="card-content-vibe-grid"><div class="vibe-icon-vibe"><?php echo esc_html($icon); ?></div><h3 class="vibe-title"><?php echo esc_html($vibe->name); ?></h3></div>
              </a>
          <?php endforeach;
      endif;
      ?>
      </div>
    </div>
  </section>

  <section class="social-proof-vibe">
    <div class="container-vibe">
      <div class="social-proof-horizontal-vibe">
        <div class="proof-stat-horizontal"><div class="stat-container-horizontal"><span class="stat-number-horizontal">50+</span><span class="stat-label-horizontal">Curated Brands</span></div></div>
        <div class="proof-stat-horizontal"><div class="stat-container-horizontal"><span class="stat-number-horizontal">Top Rated</span><span class="stat-label-horizontal">Vibe Products</span></div></div>
        <div class="proof-stat-horizontal"><div class="stat-container-horizontal"><span class="stat-number-horizontal">30-Day</span><span class="stat-label-horizontal">Returns Window</span></div></div>
      </div>
    </div>
  </section>

  <?php if ($user_vibe) : ?>
  <section class="products-section-2025" aria-labelledby="personalized-vibe-picks">
    <div class="container-vibe">
      <h2 id="personalized-vibe-picks" class="section-title-vibe"><?php esc_html_e('Recommended for your vibe', 'lumizern-vibe'); ?></h2>
      <p><a class="button" href="<?php echo esc_url(function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('vibe-profile') : home_url('/vibe-profile/')); ?>"><?php esc_html_e('Manage vibe profile', 'lumizern-vibe'); ?></a></p>
      <?php
      $personalized = wc_get_products([
        'limit' => 6,
        'status' => 'publish',
        'orderby' => 'date',
        'tax_query' => [[
          'taxonomy' => 'vibe',
          'field' => 'slug',
          'terms' => $user_vibe,
        ]],
      ]);
      ?>
      <div class="premium-trending-grid">
        <?php foreach ($personalized as $product) : ?>
          <a href="<?php echo esc_url($product->get_permalink()); ?>" class="trending-product-card">
            <div class="trending-image-wrap"><?php echo $product->get_image('woocommerce_single'); ?></div>
            <div class="trending-product-info"><h3 class="trending-product-title"><?php echo esc_html($product->get_name()); ?></h3><div class="trending-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>



  <?php $active_vibe = lumizern_get_active_vibe_slug(); if ($active_vibe) : ?>
  <section class="trending-section" aria-labelledby="trend-lab-title">
    <div class="container-vibe">
      <h2 id="trend-lab-title" class="section-title-vibe"><?php esc_html_e('Trend Lab For Your Vibe', 'lumizern-vibe'); ?></h2>
      <?php echo wp_kses_post(lumizern_render_trending_vibe_block($active_vibe, 4)); ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="trending-section" aria-labelledby="trending-section-title">
    <div class="container-vibe">
      <h2 id="trending-section-title" class="section-title-vibe"><?php esc_html_e('Trending This Week', 'lumizern-vibe'); ?></h2>
      <div class="trending-products-wrapper">
        <?php if (function_exists('wc_get_products')) :
          $products = wc_get_products(['limit' => 12, 'orderby' => 'popularity', 'order' => 'DESC', 'status' => 'publish']);
          if (!empty($products)) : ?>
            <div class="premium-trending-grid">
              <?php foreach ($products as $product) :
                $vibes = wp_get_post_terms($product->get_id(), 'vibe', ['orderby' => 'term_order']);
                $main_vibe = !empty($vibes) ? $vibes[0] : null;
                ?>
                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="trending-product-card">
                  <div class="trending-image-wrap">
                    <?php echo $product->get_image('woocommerce_single'); ?>
                    <?php if ($main_vibe) : ?><div class="trending-vibe-badge" data-vibe="<?php echo esc_attr($main_vibe->slug); ?>"><?php echo esc_html($main_vibe->name); ?></div><?php endif; ?>
                  </div>
                  <div class="trending-product-info"><h3 class="trending-product-title"><?php echo esc_html($product->get_name()); ?></h3><div class="trending-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div></div>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif;
        endif; ?>
      </div>
    </div>
  </section>
</main>
<?php get_footer();
