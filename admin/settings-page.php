<?php

/**
 * Settings page template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
  if (wp_verify_nonce($_POST['custom_plugin_nonce'], 'custom_plugin_settings')) {
    $settings = array(
      'enable_feature_1' => isset($_POST['enable_feature_1']) ? true : false,
      'enable_feature_2' => isset($_POST['enable_feature_2']) ? true : false,
      'custom_message' => sanitize_textarea_field($_POST['custom_message'])
    );

    update_option('custom_plugin_settings', $settings);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'custom-plugin') . '</p></div>';
  }
}

// Get current settings
$settings = get_option('custom_plugin_settings', array(
  'enable_feature_1' => true,
  'enable_feature_2' => false,
  'custom_message' => __('Welcome to Custom Plugin!', 'custom-plugin')
));
?>

<div class="wrap">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

  <form method="post" action="">
    <?php wp_nonce_field('custom_plugin_settings', 'custom_plugin_nonce'); ?>

    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <label for="enable_feature_1"><?php _e('Enable Feature 1', 'custom-plugin'); ?></label>
          </th>
          <td>
            <input type="checkbox" id="enable_feature_1" name="enable_feature_1" value="1" <?php checked($settings['enable_feature_1'], true); ?> />
            <p class="description"><?php _e('Enable custom meta tags in head section.', 'custom-plugin'); ?></p>
          </td>
        </tr>

        <tr>
          <th scope="row">
            <label for="enable_feature_2"><?php _e('Enable Feature 2', 'custom-plugin'); ?></label>
          </th>
          <td>
            <input type="checkbox" id="enable_feature_2" name="enable_feature_2" value="1" <?php checked($settings['enable_feature_2'], true); ?> />
            <p class="description"><?php _e('Add custom message to single posts.', 'custom-plugin'); ?></p>
          </td>
        </tr>

        <tr>
          <th scope="row">
            <label for="custom_message"><?php _e('Custom Message', 'custom-plugin'); ?></label>
          </th>
          <td>
            <textarea id="custom_message" name="custom_message" rows="4" cols="50" class="large-text"><?php echo esc_textarea($settings['custom_message']); ?></textarea>
            <p class="description"><?php _e('This message will be displayed when Feature 2 is enabled.', 'custom-plugin'); ?></p>
          </td>
        </tr>
      </tbody>
    </table>

    <?php submit_button(); ?>
  </form>

  <div class="postbox">
    <h2 class="hndle"><span><?php _e('Plugin Information', 'custom-plugin'); ?></span></h2>
    <div class="inside">
      <table class="widefat">
        <tbody>
          <tr>
            <td><strong><?php _e('Plugin Version:', 'custom-plugin'); ?></strong></td>
            <td><?php echo CUSTOM_PLUGIN_VERSION; ?></td>
          </tr>
          <tr>
            <td><strong><?php _e('WordPress Version:', 'custom-plugin'); ?></strong></td>
            <td><?php echo get_bloginfo('version'); ?></td>
          </tr>
          <tr>
            <td><strong><?php _e('PHP Version:', 'custom-plugin'); ?></strong></td>
            <td><?php echo PHP_VERSION; ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>