<?php

namespace Roots\ShareButtons\Admin;

add_action('admin_menu',  __NAMESPACE__ . '\\add_admin_menu');
add_action('admin_init',  __NAMESPACE__ . '\\settings_init');
add_action('wp_enqueue_scripts',  __NAMESPACE__ . '\\assets');

function add_admin_menu() {
  $settings_page = add_options_page('Share Buttons', 'Share Buttons', 'manage_options', 'roots_share_buttons',  __NAMESPACE__ . '\\options_page');
  add_action('load-' . $settings_page,  __NAMESPACE__ . '\\load_admin_assets');
}

function load_admin_assets() {
  add_action('admin_enqueue_scripts',  __NAMESPACE__ . '\\admin_assets');
}

function admin_assets(){
  wp_enqueue_style('roots-share-buttons-admin', plugins_url('/assets/styles/admin.css', ROOTS_SHARE_FOLDER), array());
  wp_enqueue_script('roots-share-buttons-admin', plugins_url('/assets/scripts/admin.js', ROOTS_SHARE_FOLDER), array('jquery-ui-sortable'));
}

function assets(){
  wp_enqueue_style('roots-share-buttons', plugins_url('/assets/styles/share-buttons.css', ROOTS_SHARE_FOLDER), array());
}

function settings_init() {
  register_setting(
    'roots_share_buttons',
    'roots_share_buttons',
    __NAMESPACE__ . '\\settings_sanitize'
 );
  add_settings_section(
    'roots_share_buttons_configuration',
    __('General Configuration', 'roots_share_buttons'),
    '__return_false',
    'roots_share_buttons'
  );
  add_settings_field(
    'roots_share_buttons_buttons',
    __('Enable Buttons', 'roots_share_buttons'),
    __NAMESPACE__ . '\\control_buttons',
    'roots_share_buttons',
    'roots_share_buttons_configuration'
  );
  add_settings_section(
    'roots_share_buttons_theme_integration',
    __('Theme Integration', 'roots_share_buttons'),
    '__return_false',
    'roots_share_buttons'
  );
  add_settings_field(
    'roots_share_buttons_multiple',
    __('Archive Templates', 'roots_share_buttons'),
    __NAMESPACE__ . '\\control_archive_templates',
    'roots_share_buttons',
    'roots_share_buttons_theme_integration'
  );
  add_settings_field(
    'roots_share_buttons_singular',
    __('Single Templates', 'roots_share_buttons'),
    __NAMESPACE__ . '\\control_single_templates',
    'roots_share_buttons',
    'roots_share_buttons_theme_integration'
  );
  add_settings_field(
    'roots_share_buttons_post_types',
    __('Post Types', 'roots_share_buttons'),
    __NAMESPACE__ . '\\control_post_types',
    'roots_share_buttons',
    'roots_share_buttons_theme_integration'
  );
}

function options_page() { ?>
  <div class="wrap">
    <form action="options.php" method="POST">
      <h2>Share Buttons Settings</h2>
      <?php
        settings_fields('roots_share_buttons');
        do_settings_sections('roots_share_buttons');
        submit_button();
      ?>
    </form>
  </div>
<?php
}

function get_defaults() {
  return array(
    'buttons'              => array('twitter', 'facebook', 'google_plus', 'linkedin'),
    'button_order'         => array('twitter', 'facebook', 'google_plus', 'linkedin', 'pinterest'),
    'post_types'           => array('post', 'page'),
    'archive_templates'    => array(),
    'single_templates'     => array()
 );
}

function get_settings() {
  return wp_parse_args((array) get_option('roots_share_buttons'), get_defaults());
}

function get_setting($key) {
  $settings = get_settings();
  if (isset($settings[$key])) {
    return $settings[$key];
  }
  return false;
}

function settings_sanitize($input) {
  $output = array(
    'buttons'              => array(),
    'button_order'         => array(),
    'post_types'           => array(),
    'archive_templates'    => array(),
    'single_templates'     => array()
  );

  if (isset($input['buttons'])) {
    $buttons = get_buttons();
    foreach ((array) $input['buttons'] as $button) {
      if (array_key_exists($button, $buttons)) {
        $output['buttons'][] = $button;
      }
    }
  }

  if (isset($input['button_order'])) {
    if (!is_array($input['button_order'])) {
      $input['button_order'] = explode(",", $input['button_order']);
    }

    $button_order  = array();
    $allowed_items = array(
      'twitter'     => true,
      'facebook'    => true,
      'google_plus' => true,
      'linkedin'    => true,
      'pinterest'   => true
    );

    foreach($input['button_order'] as $order_item) {
      if (isset($allowed_items[$order_item])) {
        $button_order[] = $order_item;
      }
    }

    $output['button_order'] = $button_order;
  } else {
    $output['button_order'] = array();
  }

  if (isset($input['archive_templates'])) {
    $locations = get_locations_archive();
    foreach ((array) $input['archive_templates'] as $location) {
      if (array_key_exists($location, $locations)) {
        $output['archive_templates'][] = $location;
      }
    }
  }

  if (isset($input['single_templates'])) {
    $locations = get_locations();
    foreach ((array) $input['single_templates'] as $location) {
      if (array_key_exists($location, $locations)) {
        $output['single_templates'][] = $location;
      }
    }
  }

  if (isset($input['post_types'])) {
    $post_types = get_post_types();
    foreach ((array) $input['post_types'] as $post_type) {
      if (array_key_exists($post_type, $post_types)) {
        $output['post_types'][] = $post_type;
      }
    }
  }

  return $output;
}

function get_buttons() {
  $settings = get_settings();
  $get_buttons_array = array();

  foreach($settings['button_order'] as $setting) {
    switch($setting) {
      case 'twitter':
        $get_buttons_array['twitter'] = __('Twitter', 'roots_share_buttons');
        break;
      case 'facebook':
        $get_buttons_array['facebook'] = __('Facebook', 'roots_share_buttons');
        break;
      case 'google_plus':
        $get_buttons_array['google_plus'] = __('Google Plus', 'roots_share_buttons');
        break;
      case 'linkedin':
        $get_buttons_array['linkedin'] = __('LinkedIn', 'roots_share_buttons');
        break;
      case 'pinterest':
        $get_buttons_array['pinterest'] = __('Pinterest', 'roots_share_buttons');
        break;
    }
  }
  return $get_buttons_array;
}

function get_locations() {
  return array(
    'before_content' => __('Before the content', 'roots_share_buttons'),
    'after_content'  => __('After the content', 'roots_share_buttons')
  );
}

function get_locations_archive() {
  return array(
    'before_content' => __('Before the content', 'roots_share_buttons'),
    'after_content'  => __('After the content', 'roots_share_buttons')
  );
}

function get_toggles() {
  return array(
    'enabled' => __('Enabled', 'roots_share_buttons'),
    'disabled' => __('Disabled', 'roots_share_buttons')
  );
}

function control_buttons() {
  $settings = get_settings();
  $key = 'buttons';
  $buttons = get_buttons();
  $saved = get_setting($key);

  print "\n" . '<ul id="buttons-sort" class="ui-sort">';
  foreach ($buttons as $button => $label) {
    $id = 'roots_share_buttons_' . $key . '_' . $button;
    $sort_id = str_replace('_', '', $button);
    $checked = (in_array($button, $saved)) ? ' checked="checked"' : '';
    print "\n" . '<li id="sort_' . $sort_id . '" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s">'
      .'</span><label for="' . esc_attr($id) . '"><input' . $checked . ' id="' . esc_attr($id)
      . '" type="checkbox" name="roots_share_buttons[' . $key .'][]" value="' . esc_attr($button) . '"> '
      . esc_html($label) . '</label></li>';
  }
  print "\n" . '</ul>';
  print "\n" . '<span>Drag the buttons to determine the order they will be displayed on your blog</span>';
  print "\n" . '<input type="hidden" name="roots_share_buttons[button_order]" id="roots_share_buttons_button_order" value="' . implode(',', $settings['button_order']) . '">';
}

function control_archive_templates() {
  $key = 'archive_templates';
  $settings = get_settings();
  $saved = get_setting($key);
  print "\n" . '<fieldset>';
  foreach (get_locations_archive() as $location => $label) {
    $id = 'roots_share_buttons_' . $key . '_' . $location;
    $checked = (in_array($location, $saved)) ? ' checked="checked"' : '';
    print "\n" . '<label for="' . esc_attr($id) . '"><input' . $checked . ' id="' . esc_attr($id) . '" type="checkbox" name="roots_share_buttons[' . $key . '][]" value="' . esc_attr($location) . '"> ' . esc_html($label) . '</label><br>';
  }
  print "\n" . '</fieldset>';
}

function control_single_templates() {
  $key = 'single_templates';
  $settings = get_settings();
  $saved = get_setting($key);
  print "\n" . '<fieldset>';
  foreach (get_locations() as $location => $label) {
    $id = 'roots_share_buttons_' . $key . '_' . $location;
    $checked = (in_array($location, $saved)) ? ' checked="checked"' : '';
    print "\n" . '<label for="' . esc_attr($id) . '"><input' . $checked . ' id="' . esc_attr($id) . '" type="checkbox" name="roots_share_buttons[' . $key . '][]" value="' . esc_attr($location) . '"> ' . esc_html($label) . '</label><br>';
  }
  print "\n" . '</fieldset>';
}

function control_post_types() {
  $key = 'post_types';
  $settings = get_settings();
  $saved = get_setting($key);
  print "\n" . '<fieldset>';
  foreach (get_post_types(array('public' => true)) as $post_type => $label) {
    $id = 'roots_share_buttons_' . $key . '_' . $post_type;
    $checked = (in_array($post_type, $saved)) ? ' checked="checked"' : '';
    $object = get_post_type_object($label);
    $label = $object->labels->name;
    print "\n" . '<label for="' . esc_attr($id) . '"><input' . $checked . ' id="' . esc_attr($id) . '" type="checkbox" name="roots_share_buttons[' . $key .'][]" value="' . esc_attr($post_type) . '"> ' . ucwords(esc_html($label)) . '</label><br>';
  }
  print "\n" . '</fieldset>';
}
