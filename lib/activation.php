<?php

namespace Roots\ShareButtons\Activation;

if (get_option('roots_share_buttons') === false) {
  add_option('roots_share_buttons', \Roots\ShareButtons\Admin\get_defaults());
}
