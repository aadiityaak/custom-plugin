<?php

/**
 * Add meta Boxes to the custom post type.
 *
 * @package CustomPlugin
 */

// add metabox harga to products post type
// add metabox status to orders post type

class CustomPluginMetaBox
{
  public function __construct()
  {
    add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    add_action('save_post', array($this, 'save_meta_boxes'));
  }

  /**
   * Add meta boxes
   */
  public function add_meta_boxes()
  {
    // Add meta box for Products post type
    add_meta_box(
      'custom_product_meta_box',
      __('Product Details', 'custom-plugin'),
      array($this, 'render_product_meta_box'),
      'custom_product',
      'normal',
      'high'
    );

    // Add meta box for Orders post type
    add_meta_box(
      'custom_order_meta_box',
      __('Order Details', 'custom-plugin'),
      array($this, 'render_order_meta_box'),
      'custom_order',
      'normal',
      'high'
    );
  }

  /**
   * Render meta box for Products post type
   */
  public function render_product_meta_box($post)
  {
    // Add nonce for security
    wp_nonce_field('custom_product_meta_box', 'custom_product_meta_box_nonce');

    // Get existing meta values
    $harga = get_post_meta($post->ID, '_custom_product_harga', true);
    $harga = $harga ? $harga : '';

    // Render the meta box content
    echo '<div class="custom-plugin-meta-box">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="custom_product_harga">' . __('Harga:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<div class="price-input">';
    echo '<input type="number" id="custom_product_harga" name="custom_product_harga" value="' . esc_attr($harga) . '" class="regular-text" step="0.01" min="0" placeholder="0.00" />';
    echo '</div>';
    echo '<p class="description">' . __('Masukkan harga produk dalam rupiah (contoh: 50000)', 'custom-plugin') . '</p>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
  }

  /**
   * Render meta box for Orders post type
   */
  public function render_order_meta_box($post)
  {
    // Add nonce for security
    wp_nonce_field('custom_order_meta_box', 'custom_order_meta_box_nonce');

    // Get existing meta values
    $status = get_post_meta($post->ID, '_custom_order_status', true);
    $status = $status ? $status : 'pending';

    // Define status options
    $status_options = array(
      'pending' => __('Pending', 'custom-plugin'),
      'processing' => __('Processing', 'custom-plugin'),
      'shipped' => __('Shipped', 'custom-plugin'),
      'delivered' => __('Delivered', 'custom-plugin'),
      'cancelled' => __('Cancelled', 'custom-plugin')
    );

    // Render the meta box content
    echo '<div class="custom-plugin-meta-box">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="custom_order_status">' . __('Status:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<select id="custom_order_status" name="custom_order_status" class="regular-text">';
    foreach ($status_options as $value => $label) {
      echo '<option value="' . esc_attr($value) . '"' . selected($status, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">' . __('Pilih status order saat ini', 'custom-plugin') . '</p>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
  }

  /**
   * Save meta box data
   */
  public function save_meta_boxes($post_id)
  {
    // Debug logging
    error_log('Custom Plugin: save_meta_boxes called for post ID: ' . $post_id);

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      error_log('Custom Plugin: Skipping save - autosave detected');
      return;
    }

    // Check if user has permission to save data
    if (!current_user_can('edit_post', $post_id)) {
      error_log('Custom Plugin: Skipping save - user cannot edit post');
      return;
    }

    // Get post type
    $post_type = get_post_type($post_id);
    error_log('Custom Plugin: Post type is: ' . $post_type);

    // Save Product meta box data
    if ($post_type === 'custom_product') {
      error_log('Custom Plugin: Processing product meta box');

      // Check nonce for Product meta box
      if (isset($_POST['custom_product_meta_box_nonce']) && wp_verify_nonce($_POST['custom_product_meta_box_nonce'], 'custom_product_meta_box')) {
        error_log('Custom Plugin: Product nonce verified');

        if (isset($_POST['custom_product_harga'])) {
          $harga = sanitize_text_field($_POST['custom_product_harga']);
          error_log('Custom Plugin: Saving product harga: ' . $harga);

          $result = update_post_meta($post_id, '_custom_product_harga', $harga);
          error_log('Custom Plugin: Save result: ' . ($result ? 'success' : 'failed'));
        } else {
          error_log('Custom Plugin: No harga value found in POST');
        }
      } else {
        error_log('Custom Plugin: Product nonce verification failed');
        if (!isset($_POST['custom_product_meta_box_nonce'])) {
          error_log('Custom Plugin: No nonce found in POST');
        }
      }
    }

    // Save Order meta box data
    if ($post_type === 'custom_order') {
      error_log('Custom Plugin: Processing order meta box');

      // Check nonce for Order meta box
      if (isset($_POST['custom_order_meta_box_nonce']) && wp_verify_nonce($_POST['custom_order_meta_box_nonce'], 'custom_order_meta_box')) {
        error_log('Custom Plugin: Order nonce verified');

        if (isset($_POST['custom_order_status'])) {
          $status = sanitize_text_field($_POST['custom_order_status']);
          error_log('Custom Plugin: Saving order status: ' . $status);

          $result = update_post_meta($post_id, '_custom_order_status', $status);
          error_log('Custom Plugin: Save result: ' . ($result ? 'success' : 'failed'));
        } else {
          error_log('Custom Plugin: No status value found in POST');
        }
      } else {
        error_log('Custom Plugin: Order nonce verification failed');
      }
    }
  }
}

// Initialize the meta box class
new CustomPluginMetaBox();
