<?php

namespace Roots\ShareButtons\Buttons;

add_action('template_redirect', __NAMESPACE__ . '\\integrate_single');
add_action('template_redirect', __NAMESPACE__ . '\\integrate_archive');

function integrate_single() {
  $settings = \Roots\ShareButtons\Admin\get_settings();

  if (empty($settings['single_templates'])) {
    return;
  }

  if (empty($settings['post_types'])) {
    return;
  }

  if (!is_singular($settings['post_types'])) {
    return;
  }

  foreach ($settings['single_templates'] as $location) {
    switch ($location) {
      case 'before_content':
        add_filter('the_content', __NAMESPACE__ . '\\prepend');
        break;
      case 'after_content':
        add_filter('the_content', __NAMESPACE__ . '\\append');
        break;
    }
  }
}

function integrate_archive() {
  $settings = \Roots\ShareButtons\Admin\get_settings();

  if (empty($settings['archive_templates'])) {
    return;
  }

  if (is_singular()) {
    return;
  }

  foreach ($settings['archive_templates'] as $location) {
    switch ($location) {
      case 'before_content':
        add_filter('the_content', __NAMESPACE__ . '\\prepend');
        break;
      case 'after_content':
        add_filter('the_content', __NAMESPACE__ . '\\append');
        break;
    }
  }
}

function buttons($args = array()) {
  if (is_feed()) {
    return;
  }

  ob_start();
  include(apply_filters('roots/share_template', ROOTS_SHARE_PATH . '/templates/shortcode-share.php'));
  return ob_get_clean();
}


function prepend($content) {
  if (!in_the_loop()) {
    return $content;
  }
  return buttons() . $content;
}

function prepend_archive($content) {
  $settings = \Roots\ShareButtons\Admin\get_settings();

  if (empty($settings['post_types'])) {
    return $content;
  }
  if (!in_array(get_post_type(), $settings['post_types'])) {
    return $content;
  }
  return prepend($content);
}

function append($content) {
  if (!in_the_loop()) {
    return $content;
  }
  return $content . buttons();
}

function append_archive($content) {
  $settings = \Roots\ShareButtons\Admin\get_settings();

  if (empty($settings['post_types'])) {
    return $content;
  }
  if (!in_array(get_post_type(), $settings['post_types'])) {
    return $content;
  }
  return append($content);
}
