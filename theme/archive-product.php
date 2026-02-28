<?php
if (!defined('ABSPATH')) exit;
get_header('shop');
do_action('woocommerce_before_main_content');
?>
<main class="lumizern-clean-shop">
  <section class="clean-hero"><div class="hero-content"><h1><?php esc_html_e('Lumizern Store', 'lumizern-vibe'); ?></h1><p><?php esc_html_e('Elevated essentials for every vibe.', 'lumizern-vibe'); ?></p></div></section>

  <section class="clean-vibe-bar"><div class="vibe-container">
  <?php $vibes = get_terms(['taxonomy'=>'vibe','hide_empty'=>true]); if(!is_wp_error($vibes)) foreach($vibes as $vibe): ?>
    <a href="<?php echo esc_url(get_term_link($vibe)); ?>" class="vibe-item"><img src="<?php echo esc_url(LumizernVibe2025::getVibeImageUrl($vibe->slug)); ?>" alt="<?php echo esc_attr($vibe->name); ?>"><span class="vibe-name" style="background:<?php echo esc_attr(lumizern_get_vibe_color($vibe->slug)); ?>"><?php echo esc_html($vibe->name); ?></span></a>
  <?php endforeach; ?>
  </div></section>

  <section class="clean-products"><div class="container">
  <?php if (woocommerce_product_loop()) : ?><div class="clean-grid">
    <?php while (have_posts()) : the_post(); global $product; if(!is_a($product,'WC_Product')) continue;
      $is_affiliate = get_post_meta($product->get_id(), '_is_affiliate', true) === 'yes';
      $aff_url = (string) get_post_meta($product->get_id(), '_affiliate_url', true);
      $main_vibe = wp_get_post_terms($product->get_id(), 'vibe')[0] ?? null;
      $fulfillment = lumizern_get_product_fulfillment_label($product->get_id());
      ?>
      <article class="clean-card"><a class="card-link" href="<?php echo esc_url($is_affiliate && $aff_url ? $aff_url : get_permalink()); ?>" <?php echo $is_affiliate ? 'target="_blank" rel="nofollow sponsored"' : ''; ?>>
        <div class="card-image"><?php echo $product->get_image('woocommerce_single'); ?></div>
        <?php if ($main_vibe) : ?><div class="vibe-badge-tiny" style="background:<?php echo esc_attr(lumizern_get_vibe_color($main_vibe->slug)); ?>"><?php echo esc_html($main_vibe->name); ?></div><?php endif; ?>
        <div class="card-info"><h3><?php the_title(); ?></h3><p class="catalog-fulfillment-label"><?php echo esc_html($fulfillment); ?></p><div class="price"><?php echo $is_affiliate ? 'View Product →' : wp_kses_post($product->get_price_html()); ?></div></div>
      </a>
      <?php if (!$is_affiliate && $product->is_purchasable() && $product->is_in_stock()) woocommerce_template_loop_add_to_cart(['class' => 'clean-cart ajax_add_to_cart']); ?>
      </article>
    <?php endwhile; ?>
  </div><div class="clean-pagination"><?php woocommerce_pagination(); ?></div><?php endif; ?>
  </div></section>
</main>
<?php do_action('woocommerce_after_main_content'); get_footer('shop');
