<?php

/**
 * Plugin Name: Custom Plugin
 * Plugin URI: https://velocitydeveloper.com/
 * Description: Plugin kustom untuk menambahkan fitur-fitur khusus ke website WordPress Anda.
 * Version: 1.0.0
 * Author: Velocity Developer
 * Author URI: https://velocitydeveloper.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-plugin
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

// Define plugin constants
define('CUSTOM_PLUGIN_VERSION', '1.0.0');
define('CUSTOM_PLUGIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_PLUGIN_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Custom Plugin Class
 */
class CustomPlugin
{

  /**
   * Constructor
   */
  public function __construct()
  {
    add_action('plugins_loaded', array($this, 'init'));
  }

  /**
   * Initialize the plugin
   */
  public function init()
  {
    // Load text domain for translations
    load_plugin_textdomain('custom-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Initialize hooks
    $this->init_hooks();

    // Load includes
    $this->includes();
  }

  /**
   * Initialize hooks
   */
  private function init_hooks()
  {
    // Activation and deactivation hooks
    register_activation_hook(__FILE__, array($this, 'activate'));
    register_deactivation_hook(__FILE__, array($this, 'deactivate'));

    // Admin hooks
    add_action('admin_menu', array($this, 'add_admin_menu'));
    add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

    // Frontend hooks
    add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
    add_action('init', array($this, 'init_shortcodes'));
  }

  /**
   * Include required files
   */
  private function includes()
  {
    require_once CUSTOM_PLUGIN_PLUGIN_DIR . 'includes/class-admin.php';
    require_once CUSTOM_PLUGIN_PLUGIN_DIR . 'includes/class-frontend.php';
    require_once CUSTOM_PLUGIN_PLUGIN_DIR . 'includes/class-shortcodes.php';
  }

  /**
   * Plugin activation
   */
  public function activate()
  {
    // Create database tables if needed
    $this->create_tables();

    // Set default options
    add_option('custom_plugin_version', CUSTOM_PLUGIN_VERSION);
    add_option('custom_plugin_settings', array(
      'enable_feature_1' => true,
      'enable_feature_2' => false,
      'custom_message' => __('Welcome to Custom Plugin!', 'custom-plugin')
    ));

    // Flush rewrite rules
    flush_rewrite_rules();
  }

  /**
   * Plugin deactivation
   */
  public function deactivate()
  {
    // Flush rewrite rules
    flush_rewrite_rules();
  }

  /**
   * Create database tables
   */
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

  /**
   * Add admin menu
   */
  public function add_admin_menu()
  {
    add_menu_page(
      __('Custom Plugin', 'custom-plugin'),
      __('Custom Plugin', 'custom-plugin'),
      'manage_options',
      'custom-plugin',
      array($this, 'admin_page'),
      'dashicons-admin-plugins',
      30
    );

    add_submenu_page(
      'custom-plugin',
      __('Settings', 'custom-plugin'),
      __('Settings', 'custom-plugin'),
      'manage_options',
      'custom-plugin-settings',
      array($this, 'settings_page')
    );
  }

  /**
   * Admin page callback
   */
  public function admin_page()
  {
    include CUSTOM_PLUGIN_PLUGIN_DIR . 'admin/admin-page.php';
  }

  /**
   * Settings page callback
   */
  public function settings_page()
  {
    include CUSTOM_PLUGIN_PLUGIN_DIR . 'admin/settings-page.php';
  }

  /**
   * Enqueue admin scripts and styles
   */
  public function admin_scripts($hook)
  {
    if (strpos($hook, 'custom-plugin') !== false) {
      wp_enqueue_style('custom-plugin-admin', CUSTOM_PLUGIN_PLUGIN_URL . 'assets/css/admin.css', array(), CUSTOM_PLUGIN_VERSION);
      wp_enqueue_script('custom-plugin-admin', CUSTOM_PLUGIN_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CUSTOM_PLUGIN_VERSION, true);

      wp_localize_script('custom-plugin-admin', 'customPluginAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('custom_plugin_nonce')
      ));
    }
  }

  /**
   * Enqueue frontend scripts and styles
   */
  public function frontend_scripts()
  {
    wp_enqueue_style('custom-plugin-frontend', CUSTOM_PLUGIN_PLUGIN_URL . 'assets/css/frontend.css', array(), CUSTOM_PLUGIN_VERSION);
    wp_enqueue_script('custom-plugin-frontend', CUSTOM_PLUGIN_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), CUSTOM_PLUGIN_VERSION, true);
  }

  /**
   * Initialize shortcodes
   */
  public function init_shortcodes()
  {
    new Custom_Plugin_Shortcodes();
  }
}

// Initialize the plugin
new CustomPlugin();
