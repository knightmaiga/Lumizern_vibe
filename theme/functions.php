<?php
/**
 * Lumizern Vibe 2026 - core runtime.
 */

if (!defined('ABSPATH')) {
    exit;
}

final class LumizernVibe2025
{
    private static ?LumizernVibe2025 $instance = null;
    private string $version = '1.0.0';

    public static function getInstance(): LumizernVibe2025
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->version = wp_get_theme()->get('Version') ?: $this->version;
        $this->setupHooks();
    }

    private function __clone() {}
    public function __wakeup() {}

    private function setupHooks(): void
    {
        add_action('after_setup_theme', [$this, 'themeSetup']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets'], 20);
        add_action('init', [$this, 'registerTaxonomyAndPostTypes']);

        add_action('wp_ajax_get_vibe_products', [$this, 'getVibeProducts']);
        add_action('wp_ajax_nopriv_get_vibe_products', [$this, 'getVibeProducts']);

        add_action('wp_ajax_lumizern_save_quiz_profile', [$this, 'saveQuizProfile']);
        add_action('wp_ajax_nopriv_lumizern_save_quiz_profile', [$this, 'saveQuizProfile']);

        add_action('woocommerce_product_options_general_product_data', [$this, 'addAffiliateFields']);
        add_action('woocommerce_process_product_meta', [$this, 'saveAffiliateFields']);
        add_action('woocommerce_before_shop_loop_item_title', [$this, 'affiliateBadge'], 9);
        add_filter('woocommerce_product_add_to_cart_text', [$this, 'affiliateButtonText'], 999, 2);
        add_filter('woocommerce_product_add_to_cart_url', [$this, 'affiliateButtonUrl'], 999, 2);
        add_filter('woocommerce_loop_add_to_cart_args', [$this, 'affiliateButtonTarget'], 999, 2);

        add_filter('lumizern_central_vibe_data', [$this, 'defaultCentralVibeData']);
    }

    public function themeSetup(): void
    {
        load_theme_textdomain('lumizern-vibe', get_template_directory() . '/languages');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

        register_nav_menus([
            'primary' => __('Primary Navigation', 'lumizern-vibe'),
            'footer' => __('Footer Navigation', 'lumizern-vibe'),
        ]);
    }

    public function enqueueAssets(): void
    {
        if (is_admin()) {
            return;
        }

        wp_enqueue_style('lumizern-main', get_stylesheet_uri(), [], $this->version);

        $css_paths = [
            '/assets/css/lumizern-2026.css',
            '/theme/assets/css/lumizern-2026.css',
        ];

        foreach ($css_paths as $path) {
            $file = get_stylesheet_directory() . $path;
            if (file_exists($file)) {
                wp_enqueue_style('lumizern-2026', get_stylesheet_directory_uri() . $path, ['lumizern-main'], (string) filemtime($file));
                break;
            }
        }

        $js_paths = [
            '/assets/js/lumizern-2026.js',
            '/theme/assets/js/lumizern-2026.js',
        ];

        foreach ($js_paths as $path) {
            $file = get_stylesheet_directory() . $path;
            if (file_exists($file)) {
                wp_enqueue_script('lumizern-2026', get_stylesheet_directory_uri() . $path, [], (string) filemtime($file), true);
                break;
            }
        }

        if (wp_script_is('lumizern-2026', 'enqueued')) {
            wp_localize_script('lumizern-2026', 'lumizernConfig', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('lumizern_2025_nonce'),
                'quiz_profile_nonce' => wp_create_nonce('lumizern_quiz_profile_nonce'),
            ]);
        }
    }

    public function registerTaxonomyAndPostTypes(): void
    {
        register_taxonomy('vibe', ['product', 'post', 'affiliate_post'], [
            'label' => __('Vibes', 'lumizern-vibe'),
            'public' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'vibe', 'with_front' => false, 'hierarchical' => true],
        ]);

        register_post_type('affiliate_post', [
            'labels' => [
                'name' => __('Affiliate Posts', 'lumizern-vibe'),
                'singular_name' => __('Affiliate Post', 'lumizern-vibe'),
            ],
            'public' => true,
            'has_archive' => true,
            'taxonomies' => ['vibe', 'category'],
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'rewrite' => ['slug' => 'affiliate-guides'],
            'show_in_rest' => true,
        ]);

        $this->createDefaultVibesIfNeeded();
    }

    private function createDefaultVibesIfNeeded(): void
    {
        if (get_option('lumizern_default_vibes_created') === 'yes') {
            return;
        }

        foreach (lumizern_get_vibe_registry() as $slug => $data) {
            if (!term_exists($slug, 'vibe')) {
                wp_insert_term($data['name'], 'vibe', ['slug' => $slug]);
            }
        }

        update_option('lumizern_default_vibes_created', 'yes');
    }

    public function getVibeProducts(): void
    {
        check_ajax_referer('lumizern_2025_nonce', 'nonce');

        $vibe_slug = sanitize_text_field($_POST['vibe_slug'] ?? '');
        $paged = max(1, absint($_POST['paged'] ?? 1));

        if ($vibe_slug === '') {
            wp_send_json_error(['message' => 'No vibe specified']);
        }

        $query = new WP_Query([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 15,
            'paged' => $paged,
            'tax_query' => [[
                'taxonomy' => 'vibe',
                'field' => 'slug',
                'terms' => $vibe_slug,
            ]],
        ]);

        $products = [];
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if (!$product) {
                continue;
            }

            $products[] = [
                'id' => $product->get_id(),
                'title' => $product->get_name(),
                'price' => $product->get_price_html(),
                'image' => wp_get_attachment_image_url($product->get_image_id(), 'medium') ?: wc_placeholder_img_src('medium'),
                'permalink' => get_permalink(),
                'vibes' => wp_get_post_terms($product->get_id(), 'vibe', ['fields' => 'names']),
            ];
        }
        wp_reset_postdata();

        wp_send_json_success([
            'products' => $products,
            'has_more' => $paged < $query->max_num_pages,
            'next_page' => $paged + 1,
            'current_page' => $paged,
            'total_pages' => (int) $query->max_num_pages,
        ]);
    }


    public function defaultCentralVibeData(array $data): array
    {
        if (!empty($data)) {
            return $data;
        }

        $registry = lumizern_get_vibe_registry();
        $defaults = [];

        foreach ($registry as $slug => $vibe) {
            $defaults[$slug] = [
                'emoji' => $vibe['emoji'],
                'name' => $vibe['name'],
                'color' => $vibe['color'],
                'description' => '',
                'characteristics' => [],
                'shopUrl' => $vibe['url'],
                'shopDescription' => '',
                'benefits' => [],
                'products' => [],
            ];
        }

        return $defaults;
    }

    public function saveQuizProfile(): void
    {
        check_ajax_referer('lumizern_quiz_profile_nonce', 'nonce');

        $primary = sanitize_text_field($_POST['primary'] ?? '');
        $secondary = sanitize_text_field($_POST['secondary'] ?? '');
        $tertiary = sanitize_text_field($_POST['tertiary'] ?? '');
        $email_opt_in = !empty($_POST['email_opt_in']);

        $registry = lumizern_get_vibe_registry();
        if (!isset($registry[$primary])) {
            wp_send_json_error(['message' => 'Invalid vibe profile']);
        }

        $profile = [
            'primary' => $primary,
            'secondary' => isset($registry[$secondary]) ? $secondary : '',
            'tertiary' => isset($registry[$tertiary]) ? $tertiary : '',
            'email_opt_in' => $email_opt_in ? 'yes' : 'no',
            'saved_at' => current_time('mysql'),
        ];

        if (is_user_logged_in()) {
            update_user_meta(get_current_user_id(), 'lumizern_vibe_profile', $profile);
        }

        setcookie('user_vibe_result', $profile['primary'], [
            'expires' => time() + MONTH_IN_SECONDS,
            'path' => COOKIEPATH ?: '/',
            'domain' => COOKIE_DOMAIN,
            'secure' => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        wp_send_json_success(['profile' => $profile]);
    }

    public static function getVibeImageUrl(string $slug): string
    {
        $exts = ['webp', 'jpg', 'jpeg', 'png'];
        foreach ($exts as $ext) {
            $path = get_stylesheet_directory() . '/assets/vibes/' . $slug . '.' . $ext;
            if (file_exists($path)) {
                return get_stylesheet_directory_uri() . '/assets/vibes/' . $slug . '.' . $ext;
            }
        }

        return get_stylesheet_directory_uri() . '/assets/placeholder-vibe.jpg';
    }

    public function addAffiliateFields(): void
    {
        woocommerce_wp_checkbox([
            'id' => '_is_affiliate',
            'label' => __('Affiliate / External Product', 'lumizern-vibe'),
        ]);

        woocommerce_wp_text_input([
            'id' => '_affiliate_url',
            'label' => __('Affiliate URL', 'lumizern-vibe'),
            'placeholder' => 'https://example.com/product',
            'desc_tip' => true,
        ]);
    }

    public function saveAffiliateFields(int $post_id): void
    {
        $is_affiliate = isset($_POST['_is_affiliate']) ? 'yes' : 'no';
        update_post_meta($post_id, '_is_affiliate', $is_affiliate);

        if ($is_affiliate === 'yes') {
            update_post_meta($post_id, '_affiliate_url', esc_url_raw(wp_unslash($_POST['_affiliate_url'] ?? '')));
            update_post_meta($post_id, '_price', '');
        }
    }

    public function affiliateBadge(): void
    {
        global $product;
        if ($product && get_post_meta($product->get_id(), '_is_affiliate', true) === 'yes') {
            echo '<span class="onsale affiliate-badge">Affiliate</span>';
        }
    }

    public function affiliateButtonText(string $text, WC_Product $product): string
    {
        if (get_post_meta($product->get_id(), '_is_affiliate', true) === 'yes') {
            return 'View Product →';
        }

        return $text;
    }

    public function affiliateButtonUrl(string $url, WC_Product $product): string
    {
        if (get_post_meta($product->get_id(), '_is_affiliate', true) === 'yes') {
            return (string) get_post_meta($product->get_id(), '_affiliate_url', true);
        }

        return $url;
    }

    public function affiliateButtonTarget(array $args, WC_Product $product): array
    {
        if (get_post_meta($product->get_id(), '_is_affiliate', true) === 'yes') {
            $args['attributes']['target'] = '_blank';
            $args['attributes']['rel'] = 'nofollow sponsored';
        }

        return $args;
    }
}

LumizernVibe2025::getInstance();

function lumizern_get_vibe_registry(): array
{
    return [
        'cozy-cocoon' => ['name' => 'Cozy Cocoon', 'color' => '#FF6B6B', 'emoji' => '🛋️', 'url' => '/vibe/cozy-cocoon'],
        'power-play' => ['name' => 'Power Play', 'color' => '#4ECDC4', 'emoji' => '💼', 'url' => '/vibe/power-play'],
        'aesthetic-curator' => ['name' => 'Aesthetic Curator', 'color' => '#DDA0DD', 'emoji' => '✨', 'url' => '/vibe/aesthetic-curator'],
        'zen-chill' => ['name' => 'Zen Chill', 'color' => '#FFEAA7', 'emoji' => '🧘', 'url' => '/vibe/zen-chill'],
        'creative-hustle' => ['name' => 'Creative Hustle', 'color' => '#45B7D1', 'emoji' => '🎨', 'url' => '/vibe/creative-hustle'],
        'pawfectionist' => ['name' => 'Pawfectionist', 'color' => '#96CEB4', 'emoji' => '🐾', 'url' => '/vibe/pawfectionist'],
    ];
}

function lumizern_get_vibe_color(string $slug = ''): string
{
    $registry = lumizern_get_vibe_registry();
    return $registry[$slug]['color'] ?? '#8884d8';
}

function lumizern_get_product_fulfillment_label(int $product_id): string
{
    if (get_post_meta($product_id, '_is_affiliate', true) === 'yes') {
        return __('Affiliate pick', 'lumizern-vibe');
    }

    if (get_post_meta($product_id, '_is_dropship', true) === 'yes') {
        return __('Ships from partner', 'lumizern-vibe');
    }

    return __('Ships from us', 'lumizern-vibe');
}


function lumizern_get_active_vibe_slug(): string
{
    $registry = lumizern_get_vibe_registry();

    if (is_user_logged_in()) {
        $profile = get_user_meta(get_current_user_id(), 'lumizern_vibe_profile', true);
        if (is_array($profile) && !empty($profile['primary']) && isset($registry[$profile['primary']])) {
            return $profile['primary'];
        }
    }

    $cookie = isset($_COOKIE['user_vibe_result']) ? sanitize_text_field(wp_unslash($_COOKIE['user_vibe_result'])) : '';
    return isset($registry[$cookie]) ? $cookie : '';
}

function lumizern_get_vibe_profile_data(): array
{
    $active = lumizern_get_active_vibe_slug();
    $registry = lumizern_get_vibe_registry();
    $profile = [
        'primary' => $active,
        'secondary' => '',
        'tertiary' => '',
        'email_opt_in' => 'no',
    ];

    if (is_user_logged_in()) {
        $saved = get_user_meta(get_current_user_id(), 'lumizern_vibe_profile', true);
        if (is_array($saved)) {
            foreach (['primary', 'secondary', 'tertiary', 'email_opt_in'] as $field) {
                if (isset($saved[$field])) {
                    $profile[$field] = sanitize_text_field((string) $saved[$field]);
                }
            }
        }
    }

    foreach (['primary', 'secondary', 'tertiary'] as $slug_field) {
        if (!isset($registry[$profile[$slug_field]])) {
            $profile[$slug_field] = '';
        }
    }

    return $profile;
}

function lumizern_get_personalized_products(int $limit = 6, array $exclude_ids = []): array
{
    if (!function_exists('wc_get_products')) {
        return [];
    }

    $active_vibe = lumizern_get_active_vibe_slug();
    if ($active_vibe === '') {
        return [];
    }

    return wc_get_products([
        'limit' => $limit,
        'status' => 'publish',
        'exclude' => array_map('absint', $exclude_ids),
        'tax_query' => [[
            'taxonomy' => 'vibe',
            'field' => 'slug',
            'terms' => $active_vibe,
        ]],
    ]);
}

function lumizern_register_vibe_account_endpoint(): void
{
    add_rewrite_endpoint('vibe-profile', EP_ROOT | EP_PAGES);
}
add_action('init', 'lumizern_register_vibe_account_endpoint');

function lumizern_add_vibe_query_vars(array $vars): array
{
    $vars[] = 'vibe-profile';
    return $vars;
}
add_filter('query_vars', 'lumizern_add_vibe_query_vars');

function lumizern_account_menu_item(array $items): array
{
    $logout = $items['customer-logout'] ?? null;
    unset($items['customer-logout']);
    $items['vibe-profile'] = __('My Vibe Profile', 'lumizern-vibe');
    if ($logout !== null) {
        $items['customer-logout'] = $logout;
    }
    return $items;
}
add_filter('woocommerce_account_menu_items', 'lumizern_account_menu_item');

function lumizern_account_vibe_profile_content(): void
{
    $profile = lumizern_get_vibe_profile_data();
    $registry = lumizern_get_vibe_registry();
    $current = $profile['primary'] ?: 'cozy-cocoon';

    echo '<section class="lumizern-account-vibe">';
    echo '<h2>' . esc_html__('My Vibe Profile', 'lumizern-vibe') . '</h2>';
    echo '<p>' . esc_html__('Set your vibe to personalize products, guides, and offers across the store.', 'lumizern-vibe') . '</p>';
    echo '<div class="vibe-chip-grid">';

    foreach ($registry as $slug => $vibe) {
        $active = $slug === $current ? ' is-active' : '';
        echo '<button type="button" class="vibe-chip' . esc_attr($active) . '" data-vibe-profile-option="' . esc_attr($slug) . '">';
        echo '<span>' . esc_html($vibe['emoji']) . '</span> ' . esc_html($vibe['name']) . '</button>';
    }

    echo '</div>';
    echo '<button type="button" class="button alt" data-vibe-profile-save>' . esc_html__('Save vibe profile', 'lumizern-vibe') . '</button>';
    echo '<p class="vibe-save-status" data-vibe-profile-status></p>';
    echo '</section>';
}
add_action('woocommerce_account_vibe-profile_endpoint', 'lumizern_account_vibe_profile_content');

function lumizern_rewrite_flush_on_theme_switch(): void
{
    lumizern_register_vibe_account_endpoint();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'lumizern_rewrite_flush_on_theme_switch');

function lumizern_vibe_profile_shortcode(): string
{
    $url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('vibe-profile') : home_url('/vibe-profile/');
    return '<a class="button" href="' . esc_url($url) . '">' . esc_html__('Open My Vibe Profile', 'lumizern-vibe') . '</a>';
}
add_shortcode('lumizern_vibe_profile', 'lumizern_vibe_profile_shortcode');


function lumizern_register_lead_post_type(): void
{
    register_post_type('lumizern_lead', [
        'labels' => [
            'name' => __('Newsletter Leads', 'lumizern-vibe'),
            'singular_name' => __('Newsletter Lead', 'lumizern-vibe'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'tools.php',
        'supports' => ['title'],
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
}
add_action('init', 'lumizern_register_lead_post_type');

function lumizern_subscribe_newsletter(): void
{
    check_ajax_referer('lumizern_2025_nonce', 'nonce');

    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $consent = !empty($_POST['consent']);

    if (!is_email($email) || !$consent) {
        wp_send_json_error(['message' => __('Enter a valid email and accept consent.', 'lumizern-vibe')], 400);
    }

    $existing = get_posts([
        'post_type' => 'lumizern_lead',
        'post_status' => 'publish',
        'fields' => 'ids',
        'title' => $email,
        'posts_per_page' => 1,
    ]);

    if (empty($existing)) {
        $lead_id = wp_insert_post([
            'post_type' => 'lumizern_lead',
            'post_status' => 'publish',
            'post_title' => $email,
        ]);

        if (!is_wp_error($lead_id) && $lead_id) {
            update_post_meta($lead_id, 'source', sanitize_text_field($_POST['source'] ?? 'site'));
            update_post_meta($lead_id, 'vibe', lumizern_get_active_vibe_slug());
            update_post_meta($lead_id, 'consent_at', current_time('mysql'));
        }
    }

    wp_send_json_success(['message' => __('You are in. Your premium vibe updates are on the way.', 'lumizern-vibe')]);
}
add_action('wp_ajax_lumizern_subscribe_newsletter', 'lumizern_subscribe_newsletter');
add_action('wp_ajax_nopriv_lumizern_subscribe_newsletter', 'lumizern_subscribe_newsletter');

function lumizern_get_trending_vibe_products(string $vibe_slug, int $limit = 6): array
{
    if (!function_exists('wc_get_products') || $vibe_slug === '') {
        return [];
    }

    $cache_key = 'lumizern_trending_' . md5($vibe_slug . '|' . $limit);
    $cached = get_transient($cache_key);
    if (is_array($cached)) {
        return $cached;
    }

    $products = wc_get_products([
        'limit' => $limit,
        'status' => 'publish',
        'orderby' => 'popularity',
        'tax_query' => [[
            'taxonomy' => 'vibe',
            'field' => 'slug',
            'terms' => $vibe_slug,
        ]],
    ]);

    set_transient($cache_key, $products, HOUR_IN_SECONDS);
    return $products;
}

function lumizern_render_trending_vibe_block(string $vibe_slug, int $limit = 4): string
{
    $registry = lumizern_get_vibe_registry();
    if (!isset($registry[$vibe_slug])) {
        return '';
    }

    $items = lumizern_get_trending_vibe_products($vibe_slug, $limit);
    if (empty($items)) {
        return '';
    }

    ob_start();
    ?>
    <section class="vibe-trend-lab" aria-label="<?php echo esc_attr($registry[$vibe_slug]['name']); ?> trend lab">
      <h3><?php echo esc_html($registry[$vibe_slug]['emoji'] . ' ' . sprintf(__('Trend Lab: %s', 'lumizern-vibe'), $registry[$vibe_slug]['name'])); ?></h3>
      <div class="premium-trending-grid">
        <?php foreach ($items as $product) : ?>
          <a href="<?php echo esc_url($product->get_permalink()); ?>" class="trending-product-card">
            <div class="trending-image-wrap"><?php echo $product->get_image('woocommerce_single'); ?></div>
            <div class="trending-product-info"><h4 class="trending-product-title"><?php echo esc_html($product->get_name()); ?></h4><div class="trending-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div></div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

function lumizern_trending_vibe_shortcode($atts): string
{
    $atts = shortcode_atts(['vibe' => lumizern_get_active_vibe_slug(), 'limit' => 4], $atts, 'lumizern_trending_vibe');
    return lumizern_render_trending_vibe_block(sanitize_title((string) $atts['vibe']), max(1, absint($atts['limit'])));
}
add_shortcode('lumizern_trending_vibe', 'lumizern_trending_vibe_shortcode');

function lumizern_register_trend_lab_admin_page(): void
{
    add_submenu_page(
        'tools.php',
        __('Vibe Trend Lab', 'lumizern-vibe'),
        __('Vibe Trend Lab', 'lumizern-vibe'),
        'manage_options',
        'lumizern-vibe-trend-lab',
        'lumizern_render_trend_lab_admin_page'
    );
}
add_action('admin_menu', 'lumizern_register_trend_lab_admin_page');

function lumizern_render_trend_lab_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap"><h1>' . esc_html__('Vibe Trend Lab', 'lumizern-vibe') . '</h1>';
    echo '<p>' . esc_html__('Top-performing products by vibe (popularity-sorted) to guide merchandising decisions.', 'lumizern-vibe') . '</p>';

    foreach (lumizern_get_vibe_registry() as $slug => $vibe) {
        $products = lumizern_get_trending_vibe_products($slug, 5);
        echo '<h2>' . esc_html($vibe['emoji'] . ' ' . $vibe['name']) . '</h2><ol>';
        if (empty($products)) {
            echo '<li>' . esc_html__('No products found for this vibe yet.', 'lumizern-vibe') . '</li>';
        } else {
            foreach ($products as $product) {
                echo '<li><a href="' . esc_url(get_edit_post_link($product->get_id())) . '">' . esc_html($product->get_name()) . '</a> · ' . wp_kses_post($product->get_price_html()) . '</li>';
            }
        }
        echo '</ol>';
    }

    echo '</div>';
}


function lumizern_register_sourcing_candidate_post_type(): void
{
    register_post_type('vibe_source_candidate', [
        'labels' => [
            'name' => __('Vibe Sourcing Candidates', 'lumizern-vibe'),
            'singular_name' => __('Vibe Sourcing Candidate', 'lumizern-vibe'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'tools.php',
        'supports' => ['title'],
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ]);
}
add_action('init', 'lumizern_register_sourcing_candidate_post_type');

function lumizern_calculate_curator_score(array $candidate): array
{
    $score = 0;
    $reasons = [];

    $rating = max(0, min(5, (float) ($candidate['rating'] ?? 0)));
    $reviews = max(0, (int) ($candidate['reviews'] ?? 0));
    $price = max(0, (float) ($candidate['price'] ?? 0));
    $shipping_days = max(0, (int) ($candidate['shipping_days'] ?? 0));
    $return_days = max(0, (int) ($candidate['return_days'] ?? 0));
    $premium_material = !empty($candidate['premium_material']);
    $known_brand = !empty($candidate['known_brand']);

    if ($rating >= 4.6) {
        $score += 28;
        $reasons[] = __('High customer rating', 'lumizern-vibe');
    } elseif ($rating >= 4.3) {
        $score += 20;
    } elseif ($rating >= 4.0) {
        $score += 12;
    }

    if ($reviews >= 1000) {
        $score += 22;
        $reasons[] = __('Strong review confidence', 'lumizern-vibe');
    } elseif ($reviews >= 300) {
        $score += 16;
    } elseif ($reviews >= 100) {
        $score += 10;
    }

    if ($price >= 35 && $price <= 350) {
        $score += 12;
        $reasons[] = __('Fits premium-yet-convertible price band', 'lumizern-vibe');
    } elseif ($price > 0 && $price < 15) {
        $score -= 14;
        $reasons[] = __('Price may signal low quality perception', 'lumizern-vibe');
    }

    if ($shipping_days > 0 && $shipping_days <= 5) {
        $score += 8;
    } elseif ($shipping_days > 14) {
        $score -= 10;
        $reasons[] = __('Shipping time too long for premium UX', 'lumizern-vibe');
    }

    if ($return_days >= 30) {
        $score += 8;
    } elseif ($return_days > 0 && $return_days < 14) {
        $score -= 6;
    }

    if ($premium_material) {
        $score += 8;
    }

    if ($known_brand) {
        $score += 8;
    }

    $url = (string) ($candidate['affiliate_url'] ?? '');
    if (preg_match('/aliexpress|temu|wish\./i', $url)) {
        $score -= 16;
        $reasons[] = __('Source often conflicts with premium positioning', 'lumizern-vibe');
    }

    $status = $score >= 70 ? 'approved' : ($score >= 50 ? 'review' : 'reject');

    return [
        'score' => max(0, min(100, $score)),
        'status' => $status,
        'reasons' => $reasons,
    ];
}

function lumizern_register_sourcing_lab_admin_page(): void
{
    add_submenu_page(
        'tools.php',
        __('Vibe Sourcing Lab', 'lumizern-vibe'),
        __('Vibe Sourcing Lab', 'lumizern-vibe'),
        'manage_options',
        'lumizern-vibe-sourcing-lab',
        'lumizern_render_sourcing_lab_admin_page'
    );
}
add_action('admin_menu', 'lumizern_register_sourcing_lab_admin_page');

function lumizern_handle_sourcing_candidate_save(): void
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Unauthorized', 'lumizern-vibe'));
    }

    check_admin_referer('lumizern_source_candidate');

    $title = sanitize_text_field($_POST['candidate_title'] ?? '');
    $vibe = sanitize_title($_POST['candidate_vibe'] ?? '');
    $affiliate_url = esc_url_raw(wp_unslash($_POST['candidate_affiliate_url'] ?? ''));

    if ($title === '' || $vibe === '' || $affiliate_url === '') {
        wp_safe_redirect(add_query_arg(['page' => 'lumizern-vibe-sourcing-lab', 'saved' => '0'], admin_url('tools.php')));
        exit;
    }

    $candidate = [
        'rating' => (float) ($_POST['candidate_rating'] ?? 0),
        'reviews' => (int) ($_POST['candidate_reviews'] ?? 0),
        'price' => (float) ($_POST['candidate_price'] ?? 0),
        'shipping_days' => (int) ($_POST['candidate_shipping_days'] ?? 0),
        'return_days' => (int) ($_POST['candidate_return_days'] ?? 0),
        'premium_material' => !empty($_POST['candidate_premium_material']) ? 1 : 0,
        'known_brand' => !empty($_POST['candidate_known_brand']) ? 1 : 0,
        'affiliate_url' => $affiliate_url,
        'source' => sanitize_text_field($_POST['candidate_source'] ?? ''),
    ];

    $score = lumizern_calculate_curator_score($candidate);

    $post_id = wp_insert_post([
        'post_type' => 'vibe_source_candidate',
        'post_status' => 'publish',
        'post_title' => $title,
    ]);

    if (!is_wp_error($post_id) && $post_id) {
        update_post_meta($post_id, '_candidate_vibe', $vibe);
        foreach ($candidate as $k => $v) {
            update_post_meta($post_id, '_candidate_' . $k, $v);
        }
        update_post_meta($post_id, '_candidate_score', (int) $score['score']);
        update_post_meta($post_id, '_candidate_status', $score['status']);
        update_post_meta($post_id, '_candidate_reasons', wp_json_encode($score['reasons']));
    }

    wp_safe_redirect(add_query_arg(['page' => 'lumizern-vibe-sourcing-lab', 'saved' => '1'], admin_url('tools.php')));
    exit;
}
add_action('admin_post_lumizern_save_source_candidate', 'lumizern_handle_sourcing_candidate_save');

function lumizern_render_sourcing_lab_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $registry = lumizern_get_vibe_registry();

    echo '<div class="wrap"><h1>' . esc_html__('Vibe Sourcing Lab', 'lumizern-vibe') . '</h1>';
    echo '<p>' . esc_html__('Private curator tool: add candidate products, score premium fit, and keep only high-quality vibe matches.', 'lumizern-vibe') . '</p>';

    if (isset($_GET['saved'])) {
        $class = $_GET['saved'] === '1' ? 'notice notice-success' : 'notice notice-error';
        $msg = $_GET['saved'] === '1' ? __('Candidate saved and scored.', 'lumizern-vibe') : __('Missing required fields.', 'lumizern-vibe');
        echo '<div class="' . esc_attr($class) . '"><p>' . esc_html($msg) . '</p></div>';
    }

    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="background:#fff;padding:16px;border:1px solid #ddd;max-width:860px">';
    wp_nonce_field('lumizern_source_candidate');
    echo '<input type="hidden" name="action" value="lumizern_save_source_candidate" />';

    echo '<p><label><strong>' . esc_html__('Product title', 'lumizern-vibe') . '</strong><br/><input type="text" name="candidate_title" required style="width:100%"></label></p>';
    echo '<p><label><strong>' . esc_html__('Vibe', 'lumizern-vibe') . '</strong><br/><select name="candidate_vibe" required>';
    foreach ($registry as $slug => $vibe) {
        echo '<option value="' . esc_attr($slug) . '">' . esc_html($vibe['name']) . '</option>';
    }
    echo '</select></label></p>';
    echo '<p><label><strong>' . esc_html__('Affiliate/product URL', 'lumizern-vibe') . '</strong><br/><input type="url" name="candidate_affiliate_url" required style="width:100%"></label></p>';
    echo '<p><label><strong>' . esc_html__('Source/store', 'lumizern-vibe') . '</strong><br/><input type="text" name="candidate_source" placeholder="Amazon, Brand site, etc."></label></p>';
    echo '<p><label>' . esc_html__('Price (USD)', 'lumizern-vibe') . ' <input type="number" step="0.01" min="0" name="candidate_price"></label> ';
    echo '<label>' . esc_html__('Rating (0-5)', 'lumizern-vibe') . ' <input type="number" step="0.1" min="0" max="5" name="candidate_rating"></label> ';
    echo '<label>' . esc_html__('Review count', 'lumizern-vibe') . ' <input type="number" min="0" name="candidate_reviews"></label></p>';
    echo '<p><label>' . esc_html__('Shipping days', 'lumizern-vibe') . ' <input type="number" min="0" name="candidate_shipping_days"></label> ';
    echo '<label>' . esc_html__('Return window days', 'lumizern-vibe') . ' <input type="number" min="0" name="candidate_return_days"></label></p>';
    echo '<p><label><input type="checkbox" name="candidate_premium_material" value="1"> ' . esc_html__('Premium materials / build quality verified', 'lumizern-vibe') . '</label><br/>';
    echo '<label><input type="checkbox" name="candidate_known_brand" value="1"> ' . esc_html__('Known trusted brand', 'lumizern-vibe') . '</label></p>';
    submit_button(__('Score and save candidate', 'lumizern-vibe'));
    echo '</form>';

    $candidates = get_posts([
        'post_type' => 'vibe_source_candidate',
        'post_status' => 'publish',
        'numberposts' => 50,
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    echo '<h2 style="margin-top:24px">' . esc_html__('Recent candidates', 'lumizern-vibe') . '</h2>';
    echo '<table class="widefat striped"><thead><tr><th>' . esc_html__('Product', 'lumizern-vibe') . '</th><th>' . esc_html__('Vibe', 'lumizern-vibe') . '</th><th>' . esc_html__('Score', 'lumizern-vibe') . '</th><th>' . esc_html__('Status', 'lumizern-vibe') . '</th><th>' . esc_html__('URL', 'lumizern-vibe') . '</th></tr></thead><tbody>';

    if (empty($candidates)) {
        echo '<tr><td colspan="5">' . esc_html__('No candidates yet.', 'lumizern-vibe') . '</td></tr>';
    } else {
        foreach ($candidates as $candidate_post) {
            $vibe_slug = (string) get_post_meta($candidate_post->ID, '_candidate_vibe', true);
            $score = (int) get_post_meta($candidate_post->ID, '_candidate_score', true);
            $status = (string) get_post_meta($candidate_post->ID, '_candidate_status', true);
            $url = (string) get_post_meta($candidate_post->ID, '_candidate_affiliate_url', true);
            $vibe_name = $registry[$vibe_slug]['name'] ?? $vibe_slug;

            echo '<tr>';
            echo '<td>' . esc_html($candidate_post->post_title) . '</td>';
            echo '<td>' . esc_html($vibe_name) . '</td>';
            echo '<td><strong>' . esc_html((string) $score) . '</strong></td>';
            echo '<td>' . esc_html(ucfirst($status)) . '</td>';
            echo '<td><a href="' . esc_url($url) . '" target="_blank" rel="noopener nofollow">' . esc_html__('Open', 'lumizern-vibe') . '</a></td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table></div>';
}
