<?php
/**
 * Checkout Form
 * VIBE EMPIRE 2025 - Updated for WooCommerce 9.4.0
 * @version 9.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
$order_button_text = apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'));

$user_vibe = isset($_COOKIE['user_vibe_result']) ? sanitize_text_field(wp_unslash($_COOKIE['user_vibe_result'])) : '';
$vibe_map = apply_filters('lumizern_central_vibe_data', []);
$vibe_data = $vibe_map[$user_vibe] ?? null;
?>

<div class="vibe-checkout-wrapper" data-vibe="<?php echo esc_attr($user_vibe); ?>">
    <?php if ($vibe_data) : ?>
        <div class="vibe-checkout-header" style="background: <?php echo esc_attr($vibe_data['color'] ?? '#8A2BE2'); ?>;">
            <div class="vibe-badge">
                <span class="vibe-emoji"><?php echo esc_html($vibe_data['emoji'] ?? '✨'); ?></span>
                <span><?php echo esc_html(($vibe_data['name'] ?? 'Vibe') . ' Checkout'); ?></span>
            </div>
            <p>Your energy signature is locked in</p>
        </div>
    <?php endif; ?>

    <div class="checkout-progress-vibe">
        <div class="progress-steps">
            <div class="step completed"><span>1</span> Cart</div>
            <div class="step active"><span>2</span> Details</div>
            <div class="step"><span>3</span> Payment</div>
            <div class="step"><span>4</span> Complete</div>
        </div>
        <div class="progress-bar"><div class="fill" style="width: 33%"></div></div>
    </div>

    <h1 class="checkout-title">Complete Your Vibe Order</h1>

    <?php if ($checkout->get_checkout_fields()) : ?>
    <form name="checkout" method="post" class="checkout woocommerce-checkout vibe-checkout-form" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
        <div class="checkout-layout-grid">
            <div class="checkout-main-column">
                <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                <div class="checkout-section-modern">
                    <h2 class="section-title"><span class="title-icon">👤</span> Contact & Shipping Information</h2>

                    <?php if (!is_user_logged_in() && $checkout->is_registration_enabled()) : ?>
                        <div class="checkout-login-section">
                            <p class="login-toggle-text">Already have an account? <a href="#" class="showlogin">Click here to login</a></p>
                            <?php
                            woocommerce_login_form([
                                'message'  => __('If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce'),
                                'redirect' => wc_get_checkout_url(),
                                'hidden'   => true,
                            ]);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="billing-fields-section">
                        <h3 class="subsection-title">Billing Details</h3>
                        <div class="woocommerce-billing-fields">
                            <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>
                            <div class="woocommerce-billing-fields__field-wrapper">
                                <?php foreach ($checkout->get_checkout_fields('billing') as $key => $field) {
                                    woocommerce_form_field($key, $field, $checkout->get_value($key));
                                } ?>
                            </div>
                            <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
                        </div>
                    </div>

                    <?php do_action('woocommerce_checkout_shipping_form', $checkout); ?>

                    <div class="order-notes-section">
                        <h3 class="subsection-title">Additional Information</h3>
                        <div class="woocommerce-additional-fields">
                            <?php do_action('woocommerce_before_order_notes', $checkout); ?>
                            <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
                                <div class="woocommerce-additional-fields__field-wrapper">
                                    <?php foreach ($checkout->get_checkout_fields('order') as $key => $field) {
                                        woocommerce_form_field($key, $field, $checkout->get_value($key));
                                    } ?>
                                </div>
                            <?php endif; ?>
                            <?php do_action('woocommerce_after_order_notes', $checkout); ?>
                        </div>
                    </div>
                </div>

                <?php do_action('woocommerce_checkout_after_customer_details'); ?>
            </div>

            <div class="checkout-sidebar-column">
                <div class="sticky-checkout-sidebar">
                    <div class="checkout-section-modern order-review-section">
                        <h2 class="section-title"><span class="title-icon">🛍️</span> Your Order</h2>
                        <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                        <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>
                        <div id="order_review" class="woocommerce-checkout-review-order">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>
                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>

                    <div class="checkout-section-modern payment-section">
                        <h2 class="section-title"><span class="title-icon">💳</span> Payment Method</h2>
                        <div class="woocommerce-checkout-payment">
                            <?php if (WC()->cart->needs_payment()) : ?>
                                <div class="wc_payment_methods payment_methods methods">
                                    <?php
                                    if (!empty($available_gateways)) {
                                        foreach ($available_gateways as $gateway) {
                                            wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                                        }
                                    } else {
                                        echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce')) . '</li>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-row place-order">
                                <?php wc_get_template('checkout/terms.php'); ?>
                                <?php do_action('woocommerce_review_order_before_submit'); ?>
                                <?php echo apply_filters('woocommerce_order_button_html', '<button type="submit" class="button vibe-btn-primary lz-btn alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" name="woocommerce_checkout_place_order" id="place_order" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'); ?>
                                <?php do_action('woocommerce_review_order_after_submit'); ?>
                                <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="trust-badges-checkout">
                        <div class="trust-badge"><span class="badge-icon">🔒</span><span class="badge-text">Secure SSL Encryption</span></div>
                        <div class="trust-badge"><span class="badge-icon">🌍</span><span class="badge-text">Worldwide Shipping</span></div>
                        <div class="trust-badge"><span class="badge-icon">↩️</span><span class="badge-text">30-Day Returns</span></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php else : ?>
        <div class="woocommerce-info"><?php esc_html_e('Your cart is currently empty.', 'woocommerce'); ?></div>
        <p class="return-to-shop">
            <a class="button wc-backward" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>"><?php esc_html_e('Return to shop', 'woocommerce'); ?></a>
        </p>
    <?php endif; ?>
</div>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
