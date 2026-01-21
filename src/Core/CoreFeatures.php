<?php

namespace CustomPlugin\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class CoreFeatures
 * 
 * Handles core WordPress features, theme support, and image sizes.
 */
class CoreFeatures
{

    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'register_image_sizes'));
        add_filter('upload_mimes', array($this, 'allow_svg_upload'));
    }

    /**
     * Register custom image sizes
     */
    public function register_image_sizes()
    {
        // Example: add_image_size('custom-thumb', 300, 300, true);
        // add_image_size('hero-banner', 1920, 600, true);
    }

    /**
     * Allow SVG Uploads
     */
    public function allow_svg_upload($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }
}
