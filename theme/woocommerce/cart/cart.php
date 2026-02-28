<?php
/**
 * Cart Page - VIBE EMPIRE 2025 FINAL
 * Fully Custom + WooCommerce 10.1.0 Compliant
 * @version 10.1.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>

<div class="vibe-cart-wrapper" data-theme="dark">
    <?php if (WC()->cart->is_empty()) : ?>
        <div class="vibe-empty-cart">
            <div class="empty-cart-illustration">
                <div class="cart-icon-floating">Shopping Cart</div>
                <div class="floating-shapes">
                    <div class="shape s1"></div>
                    <div class="shape s2"></div>
                    <div class="shape s3"></div>
                </div>
            </div>
            <div class="empty-cart-content">
                <h2>Your Vibe Cart is Empty</h2>
                <p class="subtitle">Curate your energy. Build your empire.</p>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="vibe-btn vibe-btn-primary">
                    <span class="btn-icon">Sparkles</span> Explore All Vibes
                </a>
                <div class="quick-vibe-links">
                    <?php
                    $vibes = get_terms(['taxonomy' => 'vibe', 'hide_empty' => false]);
                    if ($vibes && !is_wp_error($vibes)) {
                        foreach (array_slice($vibes, 0, 4) as $vibe) {
                            $color = get_term_meta($vibe->term_id, 'vibe_color', true) ?: '#8A2BE2';
                            $emoji = get_term_meta($vibe->term_id, 'vibe_emoji', true) ?: 'Crystal Ball';
                            echo '<a href="' . esc_url(get_term_link($vibe)) . '" class="quick-vibe" style="--vibe-color:' . esc_attr($color) . '">';
                            echo '<span class="vibe-emoji">' . esc_html($emoji) . '</span>';
                            echo '<span>' . esc_html($vibe->name) . '</span>';
                            echo '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="vibe-cart-header">
            <h1 class="cart-title">Your Vibe Cart</h1>
            <div class="cart-summary">
                <span class="item-count"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?> items</span>
                <span class="total-price"><?php echo wp_kses_post(WC()->cart->get_cart_total()); ?></span>
            </div>
        </div>

        <form class="woocommerce-cart-form vibe-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <div class="vibe-cart-items">
                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                        ?>
                        <div class="vibe-cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>">
                            <div class="item-remove">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" class="remove-item" aria-label="%s" data-cart_item_key="%s">×</a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_attr__('Remove this item', 'woocommerce'),
                                        esc_attr($cart_item_key)
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>

                            <div class="item-thumbnail">
                                <?php
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
                                echo $product_permalink ? sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail) : $thumbnail;
                                ?>
                            </div>

                            <div class="item-details">
                                <h3 class="item-name">
                                    <?php echo $product_permalink ? sprintf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($_product->get_name())) : wp_kses_post($_product->get_name()); ?>
                                </h3>

                                <?php
                                $vibes = wp_get_post_terms($product_id, 'vibe');
                                if ($vibes && !is_wp_error($vibes)) {
                                    echo '<div class="vibe-tags">';
                                    foreach ($vibes as $vibe) {
                                        $color = get_term_meta($vibe->term_id, 'vibe_color', true) ?: '#8A2BE2';
                                        $emoji = get_term_meta($vibe->term_id, 'vibe_emoji', true) ?: 'Crystal Ball';
                                        echo '<a href="' . esc_url(get_term_link($vibe)) . '" class="vibe-tag" style="--tag-color:' . esc_attr($color) . '">';
                                        echo '<span class="tag-emoji">' . esc_html($emoji) . '</span> ';
                                        echo esc_html($vibe->name);
                                        echo '</a> ';
                                    }
                                    echo '</div>';
                                }
                                ?>

                                <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                            </div>

                            <div class="item-price">
                                <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                            </div>

                            <div class="item-quantity">
                                <?php
                                if ($_product->is_sold_individually()) {
                                    $min = $max = 1;
                                } else {
                                    $min = 0;
                                    $max = $_product->get_max_purchase_quantity();
                                }
                                echo apply_filters('woocommerce_cart_item_quantity', woocommerce_quantity_input([
                                    'input_name'   => "cart[{$cart_item_key}][qty]",
                                    'input_value'  => $cart_item['quantity'],
                                    'max_value'    => $max,
                                    'min_value'    => $min,
                                    'product_name' => $_product->get_name(),
                                ], $_product, false), $cart_item_key, $cart_item);
                                ?>
                            </div>

                            <div class="item-subtotal">
                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <?php do_action('woocommerce_cart_contents'); ?>
            <?php do_action('woocommerce_after_cart_contents'); ?>

            <div class="vibe-cart-actions">
                <?php if (wc_coupons_enabled()) : ?>
                    <div class="coupon-input-group">
                        <input type="text" name="coupon_code" placeholder="Coupon code" id="coupon_code" class="vibe-input" />
                        <button type="submit" class="vibe-btn vibe-btn-secondary" name="apply_coupon">Apply</button>
                    </div>
                <?php endif; ?>

                <button type="submit" class="vibe-btn vibe-btn-outline" name="update_cart">Update Cart</button>
            </div>

            <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
        </form>

        <div class="cart-collaterals vibe-cart-collaterals">
            <?php do_action('woocommerce_cart_collaterals'); ?>
        </div>

        <div class="vibe-cross-sells">
            <?php woocommerce_cross_sell_display(4, 1); ?>
        </div>
    <?php endif; ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>
