<?php
if (!defined('ABSPATH')) exit;
get_header('shop');
do_action('woocommerce_before_main_content');
global $product;
if (!$product) { get_footer('shop'); return; }

$vibes = wp_get_post_terms($product->get_id(), 'vibe');
$current_vibe = isset($_GET['vibe']) ? sanitize_text_field(wp_unslash($_GET['vibe'])) : ($vibes[0]->slug ?? '');
$fulfillment = lumizern_get_product_fulfillment_label($product->get_id());
?>
<main class="container pdp">
  <?php do_action('woocommerce_before_single_product'); ?>
  <div id="product-<?php the_ID(); ?>" <?php wc_product_class('product-container', $product); ?>>
    <div class="product-grid">
      <div class="product-gallery-column"><?php do_action('woocommerce_before_single_product_summary'); ?></div>
      <div class="product-info-column">
        <h1 class="product_title"><?php the_title(); ?></h1>
        <div class="price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
        <p class="vibe-fulfillment-badge"><?php echo esc_html($fulfillment); ?></p>

        <?php if ($vibes && !is_wp_error($vibes)) : ?>
          <div class="pdp-vibe-toggle"><span class="vibe-label"><?php esc_html_e('Style this product for:', 'lumizern-vibe'); ?></span>
          <?php foreach ($vibes as $vibe) : $active = ($current_vibe === $vibe->slug) ? 'active' : ''; ?>
            <a href="<?php echo esc_url(add_query_arg('vibe', $vibe->slug)); ?>" class="vibe-filter <?php echo esc_attr($active); ?>" data-vibe="<?php echo esc_attr($vibe->slug); ?>"><?php echo esc_html($vibe->name); ?></a>
          <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="pdp-actions"><?php woocommerce_template_single_add_to_cart(); ?></div>

        <div class="trust-badges">
          <div class="trust-badge"><span>🚚</span><span><?php esc_html_e('Shipping estimates at checkout', 'lumizern-vibe'); ?></span></div>
          <div class="trust-badge"><span>↩️</span><span><?php esc_html_e('30-day return window', 'lumizern-vibe'); ?></span></div>
          <div class="trust-badge"><span>🔒</span><span><?php esc_html_e('Secure payment processing', 'lumizern-vibe'); ?></span></div>
        </div>

        <div class="pdp-desc"><?php the_content(); ?></div>

        <section class="product-faq-mini">
          <h3><?php esc_html_e('Quick FAQ', 'lumizern-vibe'); ?></h3>
          <details><summary><?php esc_html_e('How fast is shipping?', 'lumizern-vibe'); ?></summary><p><?php esc_html_e('Shipping speed depends on your location and fulfillment type shown above.', 'lumizern-vibe'); ?></p></details>
          <details><summary><?php esc_html_e('Can I return this item?', 'lumizern-vibe'); ?></summary><p><?php esc_html_e('Most items are returnable within 30 days unless stated otherwise on the product page.', 'lumizern-vibe'); ?></p></details>
        </section>
      </div>
    </div>

    <?php do_action('woocommerce_after_single_product_summary'); ?>

    <?php if (!empty($vibes) && !is_wp_error($vibes)) :
      $guide_query = new WP_Query([
        'post_type' => ['post', 'affiliate_post'],
        'posts_per_page' => 3,
        'tax_query' => [[
          'taxonomy' => 'vibe',
          'field' => 'slug',
          'terms' => $vibes[0]->slug,
        ]],
      ]);
      if ($guide_query->have_posts()) : ?>
        <section class="related-guides-vibe">
          <h3><?php esc_html_e('Guides for this vibe', 'lumizern-vibe'); ?></h3>
          <div class="premium-trending-grid">
            <?php while ($guide_query->have_posts()) : $guide_query->the_post(); ?>
              <a href="<?php the_permalink(); ?>" class="trending-product-card"><div class="trending-product-info"><h4 class="trending-product-title"><?php the_title(); ?></h4></div></a>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        </section>
      <?php endif;
    endif; ?>
  </div>
  <?php do_action('woocommerce_after_single_product'); ?>
</main>
<?php do_action('woocommerce_after_main_content'); get_footer('shop');
