<?php

namespace CustomPlugin\Core;

use CustomPlugin\Admin\Admin;
use CustomPlugin\Frontend\Frontend;
use CustomPlugin\Frontend\Shortcode;

if (!defined('ABSPATH')) {
  exit;
}

class Plugin
{

  private static $instance = null;

  public static function get_instance()
  {
    if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct()
  {
    // Hooks that need to run immediately
    add_action('plugins_loaded', array($this, 'init'));
  }

  public function init()
  {
    load_plugin_textdomain('custom-plugin', false, dirname(plugin_basename(CUSTOM_PLUGIN_FILE)) . '/languages');

    // Initialize modules
    new Admin();
    new Frontend();
    new Shortcode();
  }

  public function activate()
  {
    $this->create_tables();

    add_option('custom_plugin_version', CUSTOM_PLUGIN_VERSION);
    add_option('custom_plugin_settings', array(
      'enable_feature_1' => true,
      'enable_feature_2' => false,
      'custom_message' => __('Welcome to Custom Plugin!', 'custom-plugin')
    ));

    flush_rewrite_rules();
  }

  public function deactivate()
  {
    flush_rewrite_rules();
  }

  private function create_tables()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_plugin_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            message text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}
