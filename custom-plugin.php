<?php

/**
 * @link              dev.websweet.xyz
 * @since             1.2.0
 * @package           function-website
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Plugin
 * Plugin URI:        dev.websweet.xyz
 * Description:       Berisi fuction untuk beberapa fitur di website.
 * Version:           1.5.3
 * Author:            Imam Tri
 * Author URI:        dev.websweet.xyz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       custom-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Define plugin path url
 */
define('CUSTOM_PLUGIN_URL', plugin_dir_url(__FILE__));
require_once plugin_dir_path(__FILE__) . '/inc/enqueue.php';
require_once plugin_dir_path(__FILE__) . '/inc/function.php';
// require_once plugin_dir_path(__FILE__) . '/inc/post-type.php';
// require_once plugin_dir_path(__FILE__) . '/inc/taxonomy.php';
// require_once plugin_dir_path(__FILE__) . '/inc/metabox.php';
// require_once plugin_dir_path(__FILE__) . '/inc/collumn.php';
// require_once plugin_dir_path(__FILE__) . '/inc/admin-page.php';
// require_once plugin_dir_path(__FILE__) . '/inc/shortcodes.php';
// require_once plugin_dir_path(__FILE__) . '/inc/ajax.php';
