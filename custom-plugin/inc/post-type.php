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

// membuat post type project
function create_post_type_project()
//project
{
    register_post_type(
        'project',
        array(
            'labels' => array(
                'name' => __('Project'),
                'singular_name' => __('Project')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'project'),
            'supports' => array('title', 'thumbnail'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-building'
        )
    );
    //KARYAWAN
    register_post_type(
        'karyawan',
        array(
            'labels' => array(
                'name' => __('Karyawan'),
                'add_new'               => __('Tambah Karyawan', 'textdomain'),
                'singular_name' => __('Karyawan')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'karyawan'),
            'supports' => array('title', 'thumbnail'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-groups'
        )
    );
    //KEUANGAN
    register_post_type(
        'keuangan',
        array(
            'labels' => array(
                'name' => __('Keuangan'),
                'add_new' => __('Tambah Transaksi', 'textdomain'),
                'singular_name' => __('Keuangan')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'keuangan'),
            'supports' => array('title', 'author', 'thumbnail'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-money'
        )
    );

    //invoice
    register_post_type(
        'invoice',
        array(
            'labels' => array(
                'name' => __('Invoice'),
                'add_new' => __('Tambah Invoice', 'textdomain'),
                'singular_name' => __('Invoice')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'invoice'),
            'supports' => array('title', 'author', 'thumbnail'),
            'show_in_rest' => false,
            'menu_icon'   => 'dashicons-money'
        )
    );
}
add_action('init', 'create_post_type_project');
