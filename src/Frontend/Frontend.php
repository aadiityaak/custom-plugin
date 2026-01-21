<?php

namespace CustomPlugin\Frontend;

if (!defined('ABSPATH')) {
    exit;
}

class Frontend {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'add_meta_tags'));
        add_filter('the_content', array($this, 'modify_content'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('custom-plugin-frontend', CUSTOM_PLUGIN_URL . 'assets/frontend/css/frontend.css', array(), CUSTOM_PLUGIN_VERSION);
        wp_enqueue_script('custom-plugin-frontend', CUSTOM_PLUGIN_URL . 'assets/frontend/js/frontend.js', array('jquery'), CUSTOM_PLUGIN_VERSION, true);
    }

    public function add_meta_tags() {
        $settings = get_option('custom_plugin_settings', array());
        if (isset($settings['enable_feature_1']) && $settings['enable_feature_1']) {
            echo '<meta name="custom-plugin" content="enabled" />' . "\n";
        }
    }

    public function modify_content($content) {
        $settings = get_option('custom_plugin_settings', array());
        if (isset($settings['enable_feature_2']) && $settings['enable_feature_2']) {
            $custom_message = isset($settings['custom_message']) ? $settings['custom_message'] : '';
            if (!empty($custom_message) && is_single()) {
                $content .= '<div class="custom-plugin-message">' . esc_html($custom_message) . '</div>';
            }
        }
        return $content;
    }

    public static function display_form() {
        ob_start();
        ?>
        <div class="custom-plugin-form">
            <h3><?php _e('Contact Form', 'custom-plugin'); ?></h3>
            <form id="custom-plugin-form" method="post">
                <p>
                    <label for="cp_name"><?php _e('Name:', 'custom-plugin'); ?></label>
                    <input type="text" id="cp_name" name="name" required>
                </p>
                <p>
                    <label for="cp_email"><?php _e('Email:', 'custom-plugin'); ?></label>
                    <input type="email" id="cp_email" name="email" required>
                </p>
                <p>
                    <label for="cp_message"><?php _e('Message:', 'custom-plugin'); ?></label>
                    <textarea id="cp_message" name="message" rows="5"></textarea>
                </p>
                <p>
                    <button type="submit"><?php _e('Submit', 'custom-plugin'); ?></button>
                </p>
            </form>
            <div id="custom-plugin-response"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
