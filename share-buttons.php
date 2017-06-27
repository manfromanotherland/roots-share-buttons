<?php

namespace Roots\ShareButtons;

/*
Plugin Name:   Roots Share Buttons
Plugin URI:    https://roots.io/plugins/share-buttons/
Description:   Lightweight social sharing buttons
Version:       1.2.0
Author:        Ben Word
Author URI:    https://roots.io/
License:       MIT License
License URI:   http://opensource.org/licenses/MIT
*/

define('ROOTS_SHARE_PATH', plugin_dir_path(__FILE__));
define('ROOTS_SHARE_FOLDER', __FILE__);

require_once(__DIR__ . '/vendor/phpuri.php');
require_once(__DIR__ . '/lib/admin.php');
require_once(__DIR__ . '/lib/buttons.php');
require_once(__DIR__ . '/lib/shortcode.php');

function activation() {
  require_once(ROOTS_SHARE_PATH . 'lib/activation.php');
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\\activation');

function load_textdomain() {
  load_plugin_textdomain('roots_share_buttons', false, dirname(plugin_basename(__FILE__)) . '/lang');
}
add_action('plugins_loaded', __NAMESPACE__ . '\\load_textdomain');
