<?php
/**
 * LUMIZERN — Optimized Footer
 */
?>
<footer class="site-footer">
  <div class="footer-content">
    <div class="footer-brand">
      <p class="footer-tagline"><?php esc_html_e('AI-powered, vibe-driven, conversion-optimized e-commerce.', 'lumizern-vibe'); ?></p>
    </div>
    <div class="footer-links">
      <h3 class="footer-title"><?php esc_html_e('Shop Vibes', 'lumizern-vibe'); ?></h3>
      <ul class="footer-menu">
        <?php
        $vibes = get_terms([
          'taxonomy' => 'vibe',
          'hide_empty' => true,
          'number' => 6,
        ]);
        if (!empty($vibes) && !is_wp_error($vibes)) :
          foreach ($vibes as $vibe) : ?>
            <li><a class="footer-link" href="<?php echo esc_url(get_term_link($vibe)); ?>"><?php echo esc_html($vibe->name); ?></a></li>
          <?php endforeach;
        endif;
        ?>
      </ul>
    </div>


    <div class="footer-legal">
      <h3 class="footer-title"><?php esc_html_e('Policies', 'lumizern-vibe'); ?></h3>
      <ul class="footer-menu">
        <li><a class="footer-link" href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'lumizern-vibe'); ?></a></li>
        <li><a class="footer-link" href="<?php echo esc_url(home_url('/terms/')); ?>"><?php esc_html_e('Terms', 'lumizern-vibe'); ?></a></li>
        <li><a class="footer-link" href="<?php echo esc_url(home_url('/shipping-returns/')); ?>"><?php esc_html_e('Shipping & Returns', 'lumizern-vibe'); ?></a></li>
        <li><a class="footer-link" href="<?php echo esc_url(home_url('/affiliate-disclosure/')); ?>"><?php esc_html_e('Affiliate Disclosure', 'lumizern-vibe'); ?></a></li>
      </ul>
    </div>

    <div class="footer-newsletter">
      <h3 class="footer-title"><?php esc_html_e('Vibe Insider', 'lumizern-vibe'); ?></h3>
      <p><?php esc_html_e('Get premium trend drops, curated picks, and vibe-based buying guides.', 'lumizern-vibe'); ?></p>
      <form class="newsletter-form" data-source="footer" novalidate>
        <label for="footer-news-email" class="screen-reader-text"><?php esc_html_e('Email address', 'lumizern-vibe'); ?></label>
        <input id="footer-news-email" type="email" name="email" placeholder="you@example.com" required>
        <label><input type="checkbox" name="consent" value="1" required> <?php esc_html_e('I agree to receive email updates and offers.', 'lumizern-vibe'); ?></label>
        <button type="submit" class="button"><?php esc_html_e('Join now', 'lumizern-vibe'); ?></button>
      </form>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>.</p>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
