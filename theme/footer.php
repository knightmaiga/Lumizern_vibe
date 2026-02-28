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
  </div>
  <div class="footer-bottom">
    <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>.</p>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
