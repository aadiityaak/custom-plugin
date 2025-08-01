<?php

/**
 * Frontend functionality for Custom Plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

class Custom_Plugin_Frontend
{

  public function __construct()
  {
    add_action('wp_head', array($this, 'add_meta_tags'));
    add_filter('the_content', array($this, 'modify_content'));
  }

  /**
   * Add custom meta tags to head
   */
  public function add_meta_tags()
  {
    $settings = get_option('custom_plugin_settings', array());

    if (isset($settings['enable_feature_1']) && $settings['enable_feature_1']) {
      echo '<meta name="custom-plugin" content="enabled" />' . "\n";
    }
  }

  /**
   * Modify post content
   */
  public function modify_content($content)
  {
    $settings = get_option('custom_plugin_settings', array());

    if (isset($settings['enable_feature_2']) && $settings['enable_feature_2']) {
      $custom_message = isset($settings['custom_message']) ? $settings['custom_message'] : '';

      if (!empty($custom_message) && is_single()) {
        $content .= '<div class="custom-plugin-message">' . esc_html($custom_message) . '</div>';
      }
    }

    return $content;
  }

  /**
   * Display custom form
   */
  public static function display_form()
  {
    ob_start();
?>
    <div class="custom-plugin-form">
      <h3><?php _e('Contact Form', 'custom-plugin'); ?></h3>
      <form id="custom-plugin-form" method="post">
        <p>
          <label for="cp_name"><?php _e('Name:', 'custom-plugin'); ?></label>
          <input type="text" id="cp_name" name="name" required>
        </p>
        <p>
          <label for="cp_email"><?php _e('Email:', 'custom-plugin'); ?></label>
          <input type="email" id="cp_email" name="email" required>
        </p>
        <p>
          <label for="cp_message"><?php _e('Message:', 'custom-plugin'); ?></label>
          <textarea id="cp_message" name="message" rows="5"></textarea>
        </p>
        <p>
          <button type="submit"><?php _e('Submit', 'custom-plugin'); ?></button>
        </p>
      </form>
      <div id="custom-plugin-response"></div>
    </div>
<?php
    return ob_get_clean();
  }
}

new Custom_Plugin_Frontend();
