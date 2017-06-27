<?php

namespace Roots\ShareButtons\Shortcode;

/**
 * [share] shortcode
 */
function shortcode($atts) {
  extract(shortcode_atts(array(
    'url'   => '',
    'title' => ''
  ), $atts));

  ob_start();
  include(apply_filters('roots/share_template', ROOTS_SHARE_PATH . '/templates/shortcode-share.php'));
  return ob_get_clean();
}
add_shortcode('share',  __NAMESPACE__ . '\\shortcode');
