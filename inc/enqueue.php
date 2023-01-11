<?php

/**
 * Velocity Main Site Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Custom Plugin
 * @since 1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

function custom_plugins_enqueue_styles()
{
    wp_enqueue_style('sweetaddon-flickitty-style', 'https://unpkg.com/flickity@2/dist/flickity.min.css', array(), '1.0.0', 'all');
    wp_enqueue_style('sweetaddon-glightbox-style', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', array(), '1.0.0', 'all');
    wp_enqueue_style('custom-style', CUSTOM_PLUGIN_URL . 'assets/css/style.css', array(), '1.0.0', 'all');

    wp_enqueue_script('jQuery');
    wp_enqueue_script('</script>
      <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>');
    wp_enqueue_script('sweetaddon-flickity-script', 'https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js', array('jquery'), '1.0.0', true);
  	wp_enqueue_script('sweetaddon-printThis-script', 'https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('sweetaddon-glightbox-script', 'https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('sweetaddon-chained-script', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-chained/2.0.0-beta.2/jquery.chained.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('custom-script', CUSTOM_PLUGIN_URL . 'assets/js/script.js', array('jquery'), '1.0.0', true);
    wp_localize_script('custom-script', 'custom', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'custom_plugins_enqueue_styles', 20);

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function wpdocs_enqueue_custom_admin_style()
{
  	
  	wp_enqueue_style('admin-style', CUSTOM_PLUGIN_URL . 'assets/css/admin-style.css', array(), '1.0.0', 'all');
  
    wp_enqueue_script('jQuery');
    wp_enqueue_script('admin-script', CUSTOM_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), '1.0.0', true);
    wp_localize_script('admin-script', 'custom', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style');
