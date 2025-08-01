<?php

/**
 * Admin page template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

// Get data from database
$data = Custom_Plugin_Admin::get_all_data();
?>

<div class="wrap">
  <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

  <div class="custom-plugin-admin">
    <div class="postbox">
      <h2 class="hndle"><span><?php _e('Overview', 'custom-plugin'); ?></span></h2>
      <div class="inside">
        <p><?php _e('Welcome to Custom Plugin admin area. Here you can manage your plugin settings and view submitted data.', 'custom-plugin'); ?></p>

        <div class="custom-plugin-stats">
          <div class="stat-box">
            <h3><?php echo count($data); ?></h3>
            <p><?php _e('Total Submissions', 'custom-plugin'); ?></p>
          </div>
          <div class="stat-box">
            <h3><?php echo CUSTOM_PLUGIN_VERSION; ?></h3>
            <p><?php _e('Plugin Version', 'custom-plugin'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="postbox">
      <h2 class="hndle"><span><?php _e('Recent Submissions', 'custom-plugin'); ?></span></h2>
      <div class="inside">
        <?php if (!empty($data)): ?>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th><?php _e('ID', 'custom-plugin'); ?></th>
                <th><?php _e('Name', 'custom-plugin'); ?></th>
                <th><?php _e('Email', 'custom-plugin'); ?></th>
                <th><?php _e('Message', 'custom-plugin'); ?></th>
                <th><?php _e('Date', 'custom-plugin'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($data, 0, 10) as $row): ?>
                <tr>
                  <td><?php echo esc_html($row->id); ?></td>
                  <td><?php echo esc_html($row->name); ?></td>
                  <td><?php echo esc_html($row->email); ?></td>
                  <td><?php echo esc_html(wp_trim_words($row->message, 10)); ?></td>
                  <td><?php echo esc_html(date('Y-m-d H:i', strtotime($row->created_at))); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p><?php _e('No submissions found.', 'custom-plugin'); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="postbox">
      <h2 class="hndle"><span><?php _e('Quick Actions', 'custom-plugin'); ?></span></h2>
      <div class="inside">
        <p><?php _e('Use these shortcodes in your posts or pages:', 'custom-plugin'); ?></p>
        <ul>
          <li><code>[custom_form]</code> - <?php _e('Display contact form', 'custom-plugin'); ?></li>
          <li><code>[custom_message text="Your message"]</code> - <?php _e('Display custom message', 'custom-plugin'); ?></li>
          <li><code>[custom_data limit="5"]</code> - <?php _e('Display recent submissions', 'custom-plugin'); ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>