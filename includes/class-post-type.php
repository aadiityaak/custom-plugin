<?php

/**
 * Custom Post Type Class
 * Add Custom post type products & Orders
 *
 * @package CustomPlugin
 */

class CustomPluginPostType
{
  public function __construct()
  {
    add_action('init', array($this, 'register_post_types'));

    // Add custom columns for products
    add_filter('manage_custom_product_posts_columns', array($this, 'add_product_columns'));
    add_action('manage_custom_product_posts_custom_column', array($this, 'display_product_columns'), 10, 2);
    add_filter('manage_edit-custom_product_sortable_columns', array($this, 'make_product_columns_sortable'));

    // Add custom columns for orders
    add_filter('manage_custom_order_posts_columns', array($this, 'add_order_columns'));
    add_action('manage_custom_order_posts_custom_column', array($this, 'display_order_columns'), 10, 2);
    add_filter('manage_edit-custom_order_sortable_columns', array($this, 'make_order_columns_sortable'));

    // Handle sorting
    add_action('pre_get_posts', array($this, 'handle_custom_sorting'));

    // Handle quick edit save
    add_action('wp_ajax_save_custom_fields_quick_edit', array($this, 'save_quick_edit_fields'));

    // Handle bulk status updates
    add_action('load-edit.php', array($this, 'handle_bulk_status_update'));
  }

  /**
   * Register custom post types
   */
  public function register_post_types()
  {
    // Register Products post type
    register_post_type('custom_product', array(
      'labels' => array(
        'name' => __('Products', 'custom-plugin'),
        'singular_name' => __('Product', 'custom-plugin'),
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'products'),
      'supports' => array('title', 'editor', 'thumbnail'),
    ));

    // Register Orders post type
    register_post_type('custom_order', array(
      'labels' => array(
        'name' => __('Orders', 'custom-plugin'),
        'singular_name' => __('Order', 'custom-plugin'),
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'orders'),
      'supports' => array('title', 'editor', 'thumbnail'),
    ));
  }

  /**
   * Add custom columns for Products
   */
  public function add_product_columns($columns)
  {
    // Remove date column temporarily
    $date = $columns['date'];
    unset($columns['date']);

    // Add custom columns
    $columns['featured_image'] = __('Image', 'custom-plugin');
    $columns['product_price'] = __('Harga', 'custom-plugin');

    // Add date back at the end
    $columns['date'] = $date;

    return $columns;
  }

  /**
   * Display custom columns for Products
   */
  public function display_product_columns($column, $post_id)
  {
    switch ($column) {
      case 'featured_image':
        $thumbnail = get_the_post_thumbnail($post_id, array(60, 60));
        if ($thumbnail) {
          echo $thumbnail;
        } else {
          echo '<span style="color: #999; font-style: italic;">' . __('No image', 'custom-plugin') . '</span>';
        }
        break;

      case 'product_price':
        $harga = get_post_meta($post_id, '_custom_product_harga', true);
        if ($harga) {
          echo '<strong>Rp ' . number_format((float)$harga, 0, ',', '.') . '</strong>';
        } else {
          echo '<span style="color: #999; font-style: italic;">' . __('No price set', 'custom-plugin') . '</span>';
        }
        break;
    }
  }

  /**
   * Make product columns sortable
   */
  public function make_product_columns_sortable($columns)
  {
    $columns['product_price'] = 'product_price';
    return $columns;
  }

  /**
   * Add custom columns for Orders
   */
  public function add_order_columns($columns)
  {
    // Remove date column temporarily
    $date = $columns['date'];
    unset($columns['date']);

    // Add custom columns
    $columns['order_status'] = __('Status', 'custom-plugin');

    // Add date back at the end
    $columns['date'] = $date;

    return $columns;
  }

  /**
   * Display custom columns for Orders
   */
  public function display_order_columns($column, $post_id)
  {
    switch ($column) {
      case 'order_status':
        $status = get_post_meta($post_id, '_custom_order_status', true);
        $status = $status ? $status : 'pending';

        // Define status labels and colors
        $status_config = array(
          'pending' => array('label' => __('Pending', 'custom-plugin'), 'color' => '#f57c00'),
          'processing' => array('label' => __('Processing', 'custom-plugin'), 'color' => '#1976d2'),
          'shipped' => array('label' => __('Shipped', 'custom-plugin'), 'color' => '#7b1fa2'),
          'delivered' => array('label' => __('Delivered', 'custom-plugin'), 'color' => '#388e3c'),
          'cancelled' => array('label' => __('Cancelled', 'custom-plugin'), 'color' => '#d32f2f')
        );

        if (isset($status_config[$status])) {
          $config = $status_config[$status];
          echo '<span style="color: ' . $config['color'] . '; font-weight: bold; padding: 4px 8px; border-radius: 3px; background: rgba(' . $this->hex_to_rgb($config['color']) . ', 0.1);">';
          echo esc_html($config['label']);
          echo '</span>';
        } else {
          echo '<span style="color: #999;">' . esc_html($status) . '</span>';
        }
        break;
    }
  }

  /**
   * Make order columns sortable
   */
  public function make_order_columns_sortable($columns)
  {
    $columns['order_status'] = 'order_status';
    return $columns;
  }

  /**
   * Handle custom sorting
   */
  public function handle_custom_sorting($query)
  {
    if (!is_admin() || !$query->is_main_query()) {
      return;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'product_price') {
      $query->set('meta_key', '_custom_product_harga');
      $query->set('orderby', 'meta_value_num');
    }

    if ($orderby === 'order_status') {
      $query->set('meta_key', '_custom_order_status');
      $query->set('orderby', 'meta_value');
    }
  }

  /**
   * Convert hex color to RGB
   */
  private function hex_to_rgb($hex)
  {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "$r, $g, $b";
  }

  /**
   * Save quick edit fields via AJAX
   */
  public function save_quick_edit_fields()
  {
    // Verify nonce
    if (!wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
      wp_die(__('Security check failed', 'custom-plugin'));
    }

    $post_id = (int) $_POST['post_ID'];

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
      wp_die(__('You do not have permission to edit this post', 'custom-plugin'));
    }

    $post_type = get_post_type($post_id);

    // Save product price
    if ($post_type === 'custom_product' && isset($_POST['custom_product_harga'])) {
      $harga = sanitize_text_field($_POST['custom_product_harga']);
      update_post_meta($post_id, '_custom_product_harga', $harga);
    }

    // Save order status
    if ($post_type === 'custom_order' && isset($_POST['custom_order_status'])) {
      $status = sanitize_text_field($_POST['custom_order_status']);
      update_post_meta($post_id, '_custom_order_status', $status);
    }

    wp_die(); // Required for AJAX
  }

  /**
   * Handle bulk status updates
   */
  public function handle_bulk_status_update()
  {
    // Check if this is our bulk action
    $action = '';
    if (isset($_REQUEST['action']) && $_REQUEST['action'] != -1) {
      $action = $_REQUEST['action'];
    } elseif (isset($_REQUEST['action2']) && $_REQUEST['action2'] != -1) {
      $action = $_REQUEST['action2'];
    }

    // Only process our custom actions
    if (!in_array($action, array('mark_pending', 'mark_processing', 'mark_shipped', 'mark_delivered', 'mark_cancelled'))) {
      return;
    }

    // Check nonce
    check_admin_referer('bulk-posts');

    // Get selected posts
    $post_ids = isset($_REQUEST['post']) ? $_REQUEST['post'] : array();
    if (empty($post_ids)) {
      return;
    }

    // Extract status from action
    $status = str_replace('mark_', '', $action);

    $updated = 0;
    foreach ($post_ids as $post_id) {
      $post_id = (int) $post_id;

      // Check permissions
      if (!current_user_can('edit_post', $post_id)) {
        continue;
      }

      // Update status
      update_post_meta($post_id, '_custom_order_status', $status);
      $updated++;
    }

    // Redirect with success message
    $redirect_url = add_query_arg(array(
      'post_type' => 'custom_order',
      'bulk_status_updated' => $updated
    ), admin_url('edit.php'));

    wp_redirect($redirect_url);
    exit;
  }
}

// Initialize the custom post types
new CustomPluginPostType();
