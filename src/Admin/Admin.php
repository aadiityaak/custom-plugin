<?php

namespace CustomPlugin\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_init', array($this, 'init'));
        add_action('wp_ajax_custom_plugin_save_data', array($this, 'save_data'));
    }

    public function init() {
        register_setting('custom_plugin_settings', 'custom_plugin_settings');
    }

    public function add_admin_menu() {
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

    public function admin_page() {
        include CUSTOM_PLUGIN_DIR . 'templates/admin/admin-page.php';
    }

    public function settings_page() {
        include CUSTOM_PLUGIN_DIR . 'templates/admin/settings-page.php';
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'custom-plugin') !== false) {
            wp_enqueue_style('custom-plugin-admin', CUSTOM_PLUGIN_URL . 'assets/admin/css/admin.css', array(), CUSTOM_PLUGIN_VERSION);
            wp_enqueue_script('custom-plugin-admin', CUSTOM_PLUGIN_URL . 'assets/admin/js/admin.js', array('jquery'), CUSTOM_PLUGIN_VERSION, true);

            wp_localize_script('custom-plugin-admin', 'customPluginAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('custom_plugin_nonce')
            ));
        }
    }

    public function save_data() {
        if (!wp_verify_nonce($_POST['nonce'], 'custom_plugin_nonce')) {
            wp_die(__('Security check failed', 'custom-plugin'));
        }

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

    public static function get_all_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_plugin_data';
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    }
}
