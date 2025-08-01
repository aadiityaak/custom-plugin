<?php

/**
 * Admin functionality for Custom Plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

class Custom_Plugin_Admin
{

  public function __construct()
  {
    add_action('admin_init', array($this, 'init'));
    add_action('wp_ajax_custom_plugin_save_data', array($this, 'save_data'));
  }

  public function init()
  {
    // Register settings
    register_setting('custom_plugin_settings', 'custom_plugin_settings');
  }

  /**
   * Save data via AJAX
   */
  public function save_data()
  {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'custom_plugin_nonce')) {
      wp_die(__('Security check failed', 'custom-plugin'));
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have permission to perform this action', 'custom-plugin'));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_plugin_data';

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    $result = $wpdb->insert(
      $table_name,
      array(
        'name' => $name,
        'email' => $email,
        'message' => $message
      ),
      array('%s', '%s', '%s')
    );

    if ($result !== false) {
      wp_send_json_success(__('Data saved successfully!', 'custom-plugin'));
    } else {
      wp_send_json_error(__('Failed to save data.', 'custom-plugin'));
    }
  }

  /**
   * Get all data from database
   */
  public static function get_all_data()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_plugin_data';

    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
  }
}

new Custom_Plugin_Admin();
