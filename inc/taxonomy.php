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

// membuat taxonomy kategori project untuk post type project
function create_kategori_project_taxonomy()
{
    register_taxonomy(
        'kategori_project',
        'project',
        array(
            'label' => __('Kategori project'),
            'rewrite' => array('slug' => 'kategori-project'),
            'hierarchical' => true,
        )
    );
}
add_action('init', 'create_kategori_project_taxonomy');
