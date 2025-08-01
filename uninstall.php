<?php

/**
 * Uninstall script for Custom Plugin
 * This file is called when the plugin is deleted
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit;
}

// Delete options
delete_option('custom_plugin_version');
delete_option('custom_plugin_settings');

// Delete database table
global $wpdb;
$table_name = $wpdb->prefix . 'custom_plugin_data';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Clear any cached data
wp_cache_flush();
