<?php
if (!defined('ABSPATH')) exit;
get_header();

$term = get_queried_object();
if (!$term || $term->taxonomy !== 'vibe') {
    wp_safe_redirect(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'));
    exit;
}

$vibe_slug  = $term->slug;
$vibe_name  = $term->name;
$vibe_desc  = $term->description ?: 'Elevated living. Zero compromise.';
$vibe_color = lumizern_get_vibe_color($vibe_slug);
$vibe_image = LumizernVibe2025::getVibeImageUrl($vibe_slug);
$paged = max(1, (int) get_query_var('paged', 1));

$products = new WP_Query([
    'post_type' => 'product',
    'posts_per_page' => 15,
    'paged' => $paged,
    'tax_query' => [[
        'taxonomy' => 'vibe',
        'field' => 'slug',
        'terms' => $vibe_slug,
    ]],
]);

$guides = new WP_Query([
    'post_type' => ['post', 'affiliate_post'],
    'posts_per_page' => 6,
    'tax_query' => [[
        'taxonomy' => 'vibe',
        'field' => 'slug',
        'terms' => $vibe_slug,
    ]],
]);

$vibe_benefits = apply_filters('lumizern_vibe_benefits_detailed', [
    'cozy-cocoon' => ['Deep restorative sleep', 'Anxiety melts away', 'Wake up reborn', 'Bedroom becomes sanctuary', 'Calm in every detail'],
    'power-play' => ['Laser focus all day', 'Executive presence', 'Time expands', 'Goals become inevitable', 'You command every room'],
    'aesthetic-curator' => ['Space becomes art', 'Instagram-worthy corners', 'Minimalism meets joy', 'Guests never leave', 'You live in a moodboard'],
    'zen-chill' => ['Permanent inner peace', 'Stress has no home', 'Mindfulness without effort', 'Breathing feels expensive', 'Silence is luxury'],
    'creative-hustle' => ['Ideas flow endlessly', 'Creative blocks vanish', 'Best work daily', 'Inspiration lives here', 'You create like a god'],
    'pawfectionist' => ['Pet lives better than humans', 'Zero mess, max joy', 'Tail wags on demand', 'Pet hair controlled', 'Home smells like love'],
]);
$benefits = $vibe_benefits[$vibe_slug] ?? $vibe_benefits['cozy-cocoon'];
?>
<main class="vibe-perfection" style="--vibe-color:<?php echo esc_attr($vibe_color); ?>;padding:2rem;">
    <section class="vibe-hero">
        <div>
            <span class="vibe-badge"><?php echo esc_html($vibe_name); ?> VIBE</span>
            <h1><?php echo esc_html($vibe_name); ?></h1>
            <p><?php echo esc_html($vibe_desc); ?></p>
        </div>
        <div><img src="<?php echo esc_url($vibe_image); ?>" alt="<?php echo esc_attr($vibe_name); ?>"></div>
    </section>


    <?php $active_vibe = lumizern_get_active_vibe_slug(); if ($active_vibe && $active_vibe !== $vibe_slug) : ?>
    <section class="profile-context-bar"><div class="container-vibe">
      <p><?php esc_html_e('Your profile is set to another vibe. Compare this collection with your personalized picks.', 'lumizern-vibe'); ?></p>
      <a href="<?php echo esc_url(function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('vibe-profile') : home_url('/vibe-profile/')); ?>"><?php esc_html_e('Update profile', 'lumizern-vibe'); ?></a>
    </div></section>
    <?php endif; ?>

    <?php if ($products->have_posts()) : ?>
    <section class="editorial-section"><div class="editorial-grid">
    <?php while ($products->have_posts()) : $products->the_post(); $product = wc_get_product(get_the_ID()); if(!$product) continue;
      $is_affiliate = get_post_meta(get_the_ID(), '_is_affiliate', true) === 'yes';
      $affiliate_url = (string) get_post_meta(get_the_ID(), '_affiliate_url', true);
      ?>
      <article class="product-card">
        <a href="<?php echo esc_url($is_affiliate && $affiliate_url ? $affiliate_url : get_permalink()); ?>" <?php echo $is_affiliate ? 'target="_blank" rel="nofollow sponsored"' : ''; ?>>
            <div class="product-image-wrapper"><?php the_post_thumbnail('large', ['class' => 'product-image']); ?>
                <?php if ($is_affiliate) : ?><span class="affiliate-badge">Affiliate Link</span><?php endif; ?>
            </div>
            <div class="product-info"><h3><?php the_title(); ?></h3><div class="price"><?php echo $is_affiliate ? 'View Product →' : wp_kses_post($product->get_price_html()); ?></div></div>
        </a>
      </article>
    <?php endwhile; wp_reset_postdata(); ?>
    </div></section>
    <?php endif; ?>

    <section class="magazine-benefits">
      <div class="guides">
        <h2><?php esc_html_e('Curated Guides & Reviews', 'lumizern-vibe'); ?></h2>
        <?php while ($guides->have_posts()) : $guides->the_post(); ?>
          <article class="guide-card"><a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p></a></article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
      <aside class="benefits-sidebar"><h3><?php esc_html_e('Why This Vibe Wins', 'lumizern-vibe'); ?></h3><ul class="benefits-list"><?php foreach ($benefits as $b) : ?><li><?php echo esc_html($b); ?></li><?php endforeach; ?></ul></aside>
    </section>
</main>
<?php get_footer();
