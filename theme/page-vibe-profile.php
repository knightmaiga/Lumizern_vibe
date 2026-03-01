<?php
/* Template Name: Vibe Profile */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
$profile = lumizern_get_vibe_profile_data();
$registry = lumizern_get_vibe_registry();
$current = $profile['primary'] ?: 'cozy-cocoon';
$recommended_products = lumizern_get_personalized_products(8);
?>
<main class="lumizern-vibe-profile-page">
    <section class="vibe-profile-hero">
        <div class="container-vibe">
            <h1><?php esc_html_e('Your Vibe Profile', 'lumizern-vibe'); ?></h1>
            <p><?php esc_html_e('Pick your core vibe once and get personalized product discovery, guides, and offers everywhere.', 'lumizern-vibe'); ?></p>
        </div>
    </section>

    <section class="vibe-profile-controls">
        <div class="container-vibe">
            <div class="vibe-chip-grid">
                <?php foreach ($registry as $slug => $vibe) : ?>
                    <button type="button" class="vibe-chip <?php echo $slug === $current ? 'is-active' : ''; ?>" data-vibe-profile-option="<?php echo esc_attr($slug); ?>">
                        <span><?php echo esc_html($vibe['emoji']); ?></span>
                        <?php echo esc_html($vibe['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="vibe-profile-actions">
                <button type="button" class="button alt" data-vibe-profile-save><?php esc_html_e('Save my profile', 'lumizern-vibe'); ?></button>
                <a class="button lz-btn lz-btn-ghost" href="<?php echo esc_url(home_url('/vibe-quiz/')); ?>"><?php esc_html_e('Retake full quiz', 'lumizern-vibe'); ?></a>
                <p class="vibe-save-status" data-vibe-profile-status></p>
            </div>
        </div>
    </section>

    <section class="vibe-profile-recommendations">
        <div class="container-vibe">
            <h2><?php esc_html_e('Recommended for your profile', 'lumizern-vibe'); ?></h2>
            <div class="premium-trending-grid">
                <?php foreach ($recommended_products as $product) : ?>
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="trending-product-card">
                        <div class="trending-image-wrap"><?php echo $product->get_image('woocommerce_single'); ?></div>
                        <div class="trending-product-info">
                            <h3 class="trending-product-title"><?php echo esc_html($product->get_name()); ?></h3>
                            <div class="trending-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
