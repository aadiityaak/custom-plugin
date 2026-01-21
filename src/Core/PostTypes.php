<?php

namespace CustomPlugin\Core;

if (!defined('ABSPATH')) {
  exit;
}

class PostTypes
{

  public function __construct()
  {
    add_action('init', array($this, 'register_post_types'));
  }

  public function register_post_types()
  {
    // Example: Register 'Project' Custom Post Type
    // Uncomment the lines below to enable
    /*
        $labels = array(
            'name'                  => _x('Projects', 'Post Type General Name', 'custom-plugin'),
            'singular_name'         => _x('Project', 'Post Type Singular Name', 'custom-plugin'),
            'menu_name'             => __('Projects', 'custom-plugin'),
            'name_admin_bar'        => __('Project', 'custom-plugin'),
            'archives'              => __('Project Archives', 'custom-plugin'),
            'attributes'            => __('Project Attributes', 'custom-plugin'),
            'parent_item_colon'     => __('Parent Project:', 'custom-plugin'),
            'all_items'             => __('All Projects', 'custom-plugin'),
            'add_new_item'          => __('Add New Project', 'custom-plugin'),
            'add_new'               => __('Add New', 'custom-plugin'),
            'new_item'              => __('New Project', 'custom-plugin'),
            'edit_item'             => __('Edit Project', 'custom-plugin'),
            'update_item'           => __('Update Project', 'custom-plugin'),
            'view_item'             => __('View Project', 'custom-plugin'),
            'view_items'            => __('View Projects', 'custom-plugin'),
            'search_items'          => __('Search Project', 'custom-plugin'),
            'not_found'             => __('Not found', 'custom-plugin'),
            'not_found_in_trash'    => __('Not found in Trash', 'custom-plugin'),
            'featured_image'        => __('Featured Image', 'custom-plugin'),
            'set_featured_image'    => __('Set featured image', 'custom-plugin'),
            'remove_featured_image' => __('Remove featured image', 'custom-plugin'),
            'use_featured_image'    => __('Use as featured image', 'custom-plugin'),
            'insert_into_item'      => __('Insert into project', 'custom-plugin'),
            'uploaded_to_this_item' => __('Uploaded to this project', 'custom-plugin'),
            'items_list'            => __('Projects list', 'custom-plugin'),
            'items_list_navigation' => __('Projects list navigation', 'custom-plugin'),
            'filter_items_list'     => __('Filter projects list', 'custom-plugin'),
        );
        $args = array(
            'label'                 => __('Project', 'custom-plugin'),
            'description'           => __('Post Type Description', 'custom-plugin'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
            'taxonomies'            => array('project_category'), // Make sure this taxonomy is registered
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-portfolio', // https://developer.wordpress.org/resource/dashicons/
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true, // Enable Gutenberg
        );
        register_post_type('project', $args);
        */

    // You can add more Custom Post Types here
  }
}
