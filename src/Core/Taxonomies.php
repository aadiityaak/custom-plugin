<?php

namespace CustomPlugin\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Taxonomies
{

    public function __construct()
    {
        add_action('init', array($this, 'register_taxonomies'));
    }

    public function register_taxonomies()
    {
        // Example: Register 'project_category' Taxonomy
        // $this->register_project_category();
    }

    /**
     * Register Project Category Taxonomy
     */
    private function register_project_category()
    {
        $labels = array(
            'name'              => _x('Project Categories', 'taxonomy general name', 'custom-plugin'),
            'singular_name'     => _x('Project Category', 'taxonomy singular name', 'custom-plugin'),
            'search_items'      => __('Search Project Categories', 'custom-plugin'),
            'all_items'         => __('All Project Categories', 'custom-plugin'),
            'parent_item'       => __('Parent Project Category', 'custom-plugin'),
            'parent_item_colon' => __('Parent Project Category:', 'custom-plugin'),
            'edit_item'         => __('Edit Project Category', 'custom-plugin'),
            'update_item'       => __('Update Project Category', 'custom-plugin'),
            'add_new_item'      => __('Add New Project Category', 'custom-plugin'),
            'new_item_name'     => __('New Project Category Name', 'custom-plugin'),
            'menu_name'         => __('Project Categories', 'custom-plugin'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'project-category'),
            'show_in_rest'      => true, // Enable Gutenberg editor support
        );

        register_taxonomy('project_category', array('project'), $args);
    }
}
