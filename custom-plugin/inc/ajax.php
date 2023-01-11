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

add_action('wp_ajax_push_post_meta', 'push_post_meta');
function push_post_meta()
{
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : '';
    $meta_key = isset($_POST['meta_key']) ? $_POST['meta_key'] : '';
    $meta_value = isset($_POST['meta_value']) ? $_POST['meta_value'] : '';
    $update = update_post_meta($post_id, $meta_key, $meta_value);
    if ($update) {
        echo 'success';
    } else {
        echo 'error';
    }
    wp_die(); // this is required to terminate immediately and return a proper response
}
