<?php

/**
 * Shortcodes for Custom Plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

class Custom_Plugin_Shortcodes
{

  public function __construct()
  {
    add_shortcode('custom_form', array($this, 'custom_form_shortcode'));
    add_shortcode('custom_message', array($this, 'custom_message_shortcode'));
    add_shortcode('custom_data', array($this, 'custom_data_shortcode'));
    add_shortcode('header-search', array($this, 'custom_header_search_shortcode'));
    add_shortcode('custom_order_form', array($this, 'render_order_form'));
    add_shortcode('order_tracking', array($this, 'render_order_tracking'));
    add_shortcode('harga', array($this, 'render_harga_shortcode'));
    add_shortcode('product_categories', array($this, 'render_product_categories'));
    add_shortcode('categories_with_images', array($this, 'render_categories_with_images'));

    // AJAX handlers
    add_action('wp_ajax_submit_custom_order', array($this, 'handle_order_submission'));
    add_action('wp_ajax_nopriv_submit_custom_order', array($this, 'handle_order_submission'));
    add_action('wp_ajax_track_order', array($this, 'handle_order_tracking'));
    add_action('wp_ajax_nopriv_track_order', array($this, 'handle_order_tracking'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
  }

  /**
   * Enqueue frontend scripts for shortcode
   */
  public function enqueue_frontend_scripts()
  {
    if ($this->has_shortcode_in_content()) {
      wp_enqueue_script(
        'custom-plugin-shortcode',
        CUSTOM_PLUGIN_PLUGIN_URL . 'assets/js/shortcode.js',
        array('jquery'),
        CUSTOM_PLUGIN_VERSION,
        true
      );

      wp_enqueue_style(
        'custom-plugin-shortcode',
        CUSTOM_PLUGIN_PLUGIN_URL . 'assets/css/shortcode.css',
        array(),
        CUSTOM_PLUGIN_VERSION
      );

      // Localize script for AJAX
      wp_localize_script('custom-plugin-shortcode', 'customPluginAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('custom_order_nonce'),
        'messages' => array(
          'success' => __('Pesanan berhasil dibuat!', 'custom-plugin'),
          'error' => __('Terjadi kesalahan. Silakan coba lagi.', 'custom-plugin'),
          'loading' => __('Memproses pesanan...', 'custom-plugin')
        )
      ));
    }
  }

  /**
   * Check if shortcode exists in current content
   */
  private function has_shortcode_in_content()
  {
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'custom_order_form')) {
      return true;
    }
    return false;
  }

  public function render_harga_shortcode($atts)
  {
    global $post;

    // Parse shortcode attributes
    $atts = shortcode_atts(array(
      'product_id' => $post->ID,
    ), $atts, 'harga');

    // Get product price
    $price = get_post_meta($atts['product_id'], '_custom_product_harga', true);

    // Return formatted price
    return $price ? 'Rp ' . number_format($price, 0, ',', '.') : __('Harga tidak tersedia', 'custom-plugin');
  }

  /**
   * Render product categories shortcode
   */
  public function render_product_categories($atts)
  {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
      'show_count' => 'no',
      'hide_empty' => 'yes',
      'orderby' => 'name',
      'order' => 'ASC',
      'limit' => 0,
      'class' => '',
      'style' => 'list' // list, grid, dropdown
    ), $atts, 'product_categories');

    // Get categories
    $args = array(
      'taxonomy' => 'category_product',
      'orderby' => $atts['orderby'],
      'order' => $atts['order'],
      'hide_empty' => $atts['hide_empty'] === 'yes',
      'number' => $atts['limit'] > 0 ? $atts['limit'] : '',
    );

    $categories = get_terms($args);

    if (empty($categories) || is_wp_error($categories)) {
      return '<p>' . __('No categories found.', 'custom-plugin') . '</p>';
    }

    // Start output buffering
    ob_start();

    if ($atts['style'] === 'dropdown') {
      // Dropdown style
?>
      <div class="product-categories-dropdown <?php echo esc_attr($atts['class']); ?>">
        <select onchange="window.location.href=this.value">
          <option value=""><?php _e('Select Category', 'custom-plugin'); ?></option>
          <?php foreach ($categories as $category): ?>
            <option value="<?php echo get_term_link($category); ?>">
              <?php echo esc_html($category->name); ?>
              <?php if ($atts['show_count'] === 'yes'): ?>
                (<?php echo $category->count; ?>)
              <?php endif; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php
    } elseif ($atts['style'] === 'grid') {
      // Grid style
    ?>
      <div class="product-categories-grid <?php echo esc_attr($atts['class']); ?>">
        <?php foreach ($categories as $category): ?>
          <div class="category-item">
            <a href="<?php echo get_term_link($category); ?>" class="category-link">
              <h4><?php echo esc_html($category->name); ?></h4>
              <?php if ($category->description): ?>
                <p><?php echo esc_html($category->description); ?></p>
              <?php endif; ?>
              <?php if ($atts['show_count'] === 'yes'): ?>
                <span class="category-count"><?php echo $category->count; ?> <?php _e('products', 'custom-plugin'); ?></span>
              <?php endif; ?>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php
    } else {
      // List style (default)
    ?>
      <div class="product-categories-list <?php echo esc_attr($atts['class']); ?>">
        <ul>
          <?php foreach ($categories as $category): ?>
            <li>
              <a href="<?php echo get_term_link($category); ?>">
                <?php echo esc_html($category->name); ?>
                <?php if ($atts['show_count'] === 'yes'): ?>
                  <span class="count">(<?php echo $category->count; ?>)</span>
                <?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php
    }

    return ob_get_clean();
  }

  /**
   * Render order form shortcode
   */
  public function render_order_form($atts)
  {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
      'product_id' => 0,
      'show_product_select' => 'yes',
      'title' => __('Form Pemesanan', 'custom-plugin'),
      'submit_text' => __('Buat Pesanan', 'custom-plugin'),
      'class' => ''
    ), $atts, 'custom_order_form');

    // Get current product ID if on single product page
    if (is_singular('custom_product') && empty($atts['product_id'])) {
      global $post;
      $atts['product_id'] = $post->ID;
      $atts['show_product_select'] = 'no';
    }

    // Get all products
    $products = get_posts(array(
      'post_type' => 'custom_product',
      'posts_per_page' => -1,
      'post_status' => 'publish'
    ));

    // Start output buffering
    ob_start();
    ?>
    <div class="custom-order-form-container <?php echo esc_attr($atts['class']); ?>">
      <form id="custom-order-form" class="custom-order-form" method="post">
        <?php wp_nonce_field('custom_order_nonce', 'custom_order_nonce_field'); ?>

        <h3 class="form-title"><?php echo esc_html($atts['title']); ?></h3>

        <div class="form-section">
          <h4><?php _e('Informasi Produk', 'custom-plugin'); ?></h4>

          <?php if ($atts['show_product_select'] === 'yes'): ?>
            <div class="form-group">
              <label for="order_product_id"><?php _e('Pilih Produk:', 'custom-plugin'); ?> <span class="required">*</span></label>
              <select id="order_product_id" name="order_product_id" required>
                <option value=""><?php _e('-- Pilih Produk --', 'custom-plugin'); ?></option>
                <?php foreach ($products as $product):
                  $price = get_post_meta($product->ID, '_custom_product_harga', true);
                ?>
                  <option value="<?php echo esc_attr($product->ID); ?>"
                    data-price="<?php echo esc_attr($price); ?>"
                    <?php selected($atts['product_id'], $product->ID); ?>>
                    <?php echo esc_html($product->post_title); ?>
                    <?php if ($price): ?>
                      - Rp <?php echo number_format($price, 0, ',', '.'); ?>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php else: ?>
            <input type="hidden" id="order_product_id" name="order_product_id" value="<?php echo esc_attr($atts['product_id']); ?>">
            <div class="selected-product-info">
              <?php
              $selected_product = get_post($atts['product_id']);
              if ($selected_product):
                $price = get_post_meta($selected_product->ID, '_custom_product_harga', true);
              ?>
                <h5><?php _e('Produk Terpilih:', 'custom-plugin'); ?></h5>
                <div class="product-card">
                  <?php if (has_post_thumbnail($selected_product->ID)): ?>
                    <div class="product-image">
                      <?php echo get_the_post_thumbnail($selected_product->ID, 'thumbnail'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="product-details">
                    <h6><?php echo esc_html($selected_product->post_title); ?></h6>
                    <?php if ($price): ?>
                      <p class="product-price" data-price="<?php echo esc_attr($price); ?>">Rp <?php echo number_format($price, 0, ',', '.'); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <div class="form-group">
            <label for="order_quantity"><?php _e('Jumlah:', 'custom-plugin'); ?> <span class="required">*</span></label>
            <input type="number" id="order_quantity" name="order_quantity" min="1" value="1" required>
          </div>

          <div class="form-group">
            <label for="order_total_display"><?php _e('Total Harga:', 'custom-plugin'); ?></label>
            <div class="total-price-display">
              <span id="total_price_text">Rp 0</span>
            </div>
            <input type="hidden" id="order_total_price" name="order_total_price" value="0">
          </div>
        </div>

        <div class="form-section">
          <h4><?php _e('Informasi Pemesan', 'custom-plugin'); ?></h4>

          <div class="form-group">
            <label for="customer_name"><?php _e('Nama Lengkap:', 'custom-plugin'); ?> <span class="required">*</span></label>
            <input type="text" id="customer_name" name="customer_name" required>
          </div>

          <div class="form-group">
            <label for="customer_email"><?php _e('Email:', 'custom-plugin'); ?> <span class="required">*</span></label>
            <input type="email" id="customer_email" name="customer_email" required>
          </div>

          <div class="form-group">
            <label for="customer_phone"><?php _e('No. Telepon:', 'custom-plugin'); ?> <span class="required">*</span></label>
            <input type="tel" id="customer_phone" name="customer_phone" placeholder="08xxxxxxxxxx" required>
          </div>

          <div class="form-group">
            <label for="customer_address"><?php _e('Alamat Lengkap:', 'custom-plugin'); ?> <span class="required">*</span></label>
            <textarea id="customer_address" name="customer_address" rows="4" required></textarea>
          </div>
        </div>

        <div class="form-section">
          <h4><?php _e('Catatan Tambahan', 'custom-plugin'); ?></h4>

          <div class="form-group">
            <label for="order_notes"><?php _e('Catatan / Permintaan Khusus:', 'custom-plugin'); ?></label>
            <textarea id="order_notes" name="order_notes" rows="3" placeholder="<?php _e('Masukkan catatan atau permintaan khusus untuk pesanan Anda...', 'custom-plugin'); ?>"></textarea>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="submit-order-btn">
            <span class="btn-text"><?php echo esc_html($atts['submit_text']); ?></span>
            <span class="btn-loading" style="display: none;">
              <span class="spinner"></span>
              <?php _e('Memproses...', 'custom-plugin'); ?>
            </span>
          </button>
        </div>

        <div class="form-messages">
          <div class="success-message" style="display: none;"></div>
          <div class="error-message" style="display: none;"></div>
        </div>
      </form>
    </div>
  <?php
    return ob_get_clean();
  }

  /**
   * Handle order form submission via AJAX
   */
  public function handle_order_submission()
  {
    // Verify nonce
    if (!wp_verify_nonce($_POST['custom_order_nonce_field'], 'custom_order_nonce')) {
      wp_die(__('Security check failed', 'custom-plugin'));
    }

    // Sanitize input data
    $product_id = intval($_POST['order_product_id']);
    $quantity = intval($_POST['order_quantity']);
    $total_price = floatval($_POST['order_total_price']);
    $customer_name = sanitize_text_field($_POST['customer_name']);
    $customer_email = sanitize_email($_POST['customer_email']);
    $customer_phone = sanitize_text_field($_POST['customer_phone']);
    $customer_address = sanitize_textarea_field($_POST['customer_address']);
    $order_notes = sanitize_textarea_field($_POST['order_notes']);

    // Validate required fields
    if (empty($product_id) || empty($quantity) || empty($customer_name) || empty($customer_email) || empty($customer_phone) || empty($customer_address)) {
      wp_send_json_error(array(
        'message' => __('Semua field yang wajib harus diisi.', 'custom-plugin')
      ));
    }

    // Get product info
    $product = get_post($product_id);
    if (!$product || $product->post_type !== 'custom_product') {
      wp_send_json_error(array(
        'message' => __('Produk tidak valid.', 'custom-plugin')
      ));
    }

    // Generate invoice ID
    $invoice_id = 'INV-' . date('Y') . '-' . str_pad(wp_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Create order post
    $order_data = array(
      'post_title' => $invoice_id,
      'post_content' => $order_notes,
      'post_status' => 'publish',
      'post_type' => 'custom_order',
      'post_author' => get_current_user_id(),
    );

    $order_id = wp_insert_post($order_data);

    if (is_wp_error($order_id)) {
      wp_send_json_error(array(
        'message' => __('Gagal membuat pesanan. Silakan coba lagi.', 'custom-plugin')
      ));
    }

    // Save order meta data
    update_post_meta($order_id, '_custom_order_product_id', $product_id);
    update_post_meta($order_id, '_custom_order_jumlah', $quantity);
    update_post_meta($order_id, '_custom_order_total_harga', $total_price);
    update_post_meta($order_id, '_custom_order_status', 'pending');
    update_post_meta($order_id, '_custom_order_nama_pemesan', $customer_name);
    update_post_meta($order_id, '_custom_order_no_hp', $customer_phone);
    update_post_meta($order_id, '_custom_order_alamat', $customer_address);
    update_post_meta($order_id, '_custom_order_email', $customer_email);

    // Initialize timeline with first step
    $timeline_data = array(
      1 => array(
        'date' => date('Y-m-d'),
        'time' => date('H:i'),
        'completed' => true
      )
    );
    update_post_meta($order_id, '_custom_order_timeline', $timeline_data);

    // Send success response
    wp_send_json_success(array(
      'message' => sprintf(__('Pesanan berhasil dibuat dengan ID: %s', 'custom-plugin'), $invoice_id),
      'order_id' => $order_id,
      'invoice_id' => $invoice_id,
      'redirect_url' => get_permalink($order_id)
    ));
  }

  /**
   * Render order tracking shortcode
   */
  public function render_order_tracking($atts)
  {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
      'title' => __('Lacak Pesanan', 'custom-plugin'),
      'placeholder' => __('Masukkan nomor invoice (contoh: INV-2024-001)', 'custom-plugin'),
      'button_text' => __('Lacak Pesanan', 'custom-plugin'),
      'class' => ''
    ), $atts, 'order_tracking');

    // Start output buffering
    ob_start();
  ?>
    <div class="order-tracking-container <?php echo esc_attr($atts['class']); ?>">
      <div class="tracking-search-form">
        <form id="order-tracking-form" class="tracking-form">
          <?php wp_nonce_field('track_order_nonce', 'track_order_nonce_field'); ?>
          <div class="tracking-input-group">
            <input type="text"
              id="invoice_number"
              name="invoice_number"
              placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
              class="tracking-input"
              required>
            <button type="submit" class="tracking-btn">
              <span class="btn-text"><?php echo esc_html($atts['button_text']); ?></span>
              <span class="btn-loading" style="display: none;">
                <span class="spinner"></span>
                <?php _e('Mencari...', 'custom-plugin'); ?>
              </span>
            </button>
          </div>
        </form>
      </div>

      <div class="tracking-results" style="display: none;">
        <div class="tracking-header">
          <h4><?php _e('Status Pengiriman', 'custom-plugin'); ?></h4>
          <div class="order-info">
            <div class="order-detail">
              <span class="label"><?php _e('No. Invoice:', 'custom-plugin'); ?></span>
              <span class="value invoice-value"></span>
            </div>
            <div class="order-detail">
              <span class="label"><?php _e('Status:', 'custom-plugin'); ?></span>
              <span class="value status-badge"></span>
            </div>
          </div>
        </div>

        <div class="tracking-timeline">
          <div class="timeline-header">
            <h5><?php _e('Riwayat Pengiriman', 'custom-plugin'); ?></h5>
          </div>
          <div class="timeline-content">
            <!-- Timeline will be populated via AJAX -->
          </div>
        </div>

        <div class="delivery-info" style="display: none;">
          <h5><?php _e('Informasi Pengiriman', 'custom-plugin'); ?></h5>
          <div class="delivery-details">
            <div class="driver-info">
              <span class="label"><?php _e('Driver:', 'custom-plugin'); ?></span>
              <span class="driver-name"></span>
            </div>
            <div class="driver-phone">
              <span class="label"><?php _e('No. Telepon:', 'custom-plugin'); ?></span>
              <span class="phone-number"></span>
            </div>
          </div>
        </div>

        <div class="proof-delivery" style="display: none;">
          <h5><?php _e('Bukti Penerimaan', 'custom-plugin'); ?></h5>
          <div class="proof-image">
            <!-- Delivery proof image will be shown here -->
          </div>
        </div>
      </div>

      <div class="tracking-messages">
        <div class="success-message" style="display: none;"></div>
        <div class="error-message" style="display: none;"></div>
      </div>
    </div>
  <?php
    return ob_get_clean();
  }

  /**
   * Handle order tracking via AJAX
   */
  public function handle_order_tracking()
  {
    // Verify nonce
    if (!wp_verify_nonce($_POST['track_order_nonce_field'], 'track_order_nonce')) {
      wp_die(__('Security check failed', 'custom-plugin'));
    }

    // Sanitize input
    $invoice_number = sanitize_text_field($_POST['invoice_number']);

    if (empty($invoice_number)) {
      wp_send_json_error(array(
        'message' => __('Nomor invoice harus diisi.', 'custom-plugin')
      ));
    }

    // Search for order by title (invoice number)
    $orders = get_posts(array(
      'post_type' => 'custom_order',
      'post_status' => 'publish',
      'title' => $invoice_number,
      'posts_per_page' => 1
    ));

    if (empty($orders)) {
      wp_send_json_error(array(
        'message' => __('Pesanan tidak ditemukan. Pastikan nomor invoice benar.', 'custom-plugin')
      ));
    }

    $order = $orders[0];
    $order_id = $order->ID;

    // Get order details
    $status = get_post_meta($order_id, '_custom_order_status', true);
    $customer_name = get_post_meta($order_id, '_custom_order_nama_pemesan', true);
    $customer_phone = get_post_meta($order_id, '_custom_order_no_hp', true);
    $timeline = get_post_meta($order_id, '_custom_order_timeline', true);
    $timeline = $timeline ? $timeline : array();

    // Timeline steps labels
    $timeline_steps = array(
      1 => 'Pesanan diterima dan diteruskan ke bagian produksi',
      2 => 'Konfirmasi desain',
      3 => 'Konfirmasi warna',
      4 => 'Progres 25 persen selesai',
      5 => 'Progres 50 persen selesai',
      6 => 'Progres 75 persen selesai',
      7 => 'Pesanan selesai dicetak',
      8 => 'Proses packing dan QC',
      9 => 'Pesanan disiapkan oleh tim delivery',
      10 => 'Pesanan diantarkan dan otw menuju lokasi',
      11 => 'Pesanan diterima (terlampir bukti foto)'
    );

    // Build timeline HTML
    $timeline_html = '';
    foreach ($timeline_steps as $step_num => $step_label) {
      $step_data = isset($timeline[$step_num]) ? $timeline[$step_num] : null;
      $is_completed = $step_data && isset($step_data['date']) && isset($step_data['time']);

      $timeline_html .= '<div class="timeline-item ' . ($is_completed ? 'completed' : 'pending') . '">';
      $timeline_html .= '<div class="timeline-marker"></div>';
      $timeline_html .= '<div class="timeline-content">';
      $timeline_html .= '<div class="timeline-title">' . esc_html($step_label) . '</div>';

      if ($is_completed) {
        $datetime = $step_data['date'] . ' ' . $step_data['time'];
        $timeline_html .= '<div class="timeline-date">' . date('d/m/Y H:i', strtotime($datetime)) . '</div>';
      } else {
        $timeline_html .= '<div class="timeline-date pending-text">Menunggu</div>';
      }

      $timeline_html .= '</div>';
      $timeline_html .= '</div>';
    }

    // Get delivery info (step 10)
    $delivery_info = array();
    if (isset($timeline[10])) {
      $delivery_info = array(
        'driver_name' => isset($timeline[10]['driver_name']) ? $timeline[10]['driver_name'] : '',
        'driver_phone' => isset($timeline[10]['driver_phone']) ? $timeline[10]['driver_phone'] : ''
      );
    }

    // Get proof image (step 11)
    $proof_image = '';
    if (isset($timeline[11]['photo_proof'])) {
      $proof_image = wp_get_attachment_url($timeline[11]['photo_proof']);
    }

    // Status labels
    $status_labels = array(
      'pending' => __('Menunggu', 'custom-plugin'),
      'processing' => __('Diproses', 'custom-plugin'),
      'shipped' => __('Dikirim', 'custom-plugin'),
      'delivered' => __('Terkirim', 'custom-plugin'),
      'cancelled' => __('Dibatalkan', 'custom-plugin')
    );

    wp_send_json_success(array(
      'invoice' => $invoice_number,
      'status' => $status,
      'status_label' => isset($status_labels[$status]) ? $status_labels[$status] : ucfirst($status),
      'customer_name' => $customer_name,
      'customer_phone' => $customer_phone,
      'timeline_html' => $timeline_html,
      'delivery_info' => $delivery_info,
      'proof_image' => $proof_image
    ));
  }

  /**
   * Custom header search shortcode
   */
  public function custom_header_search_shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'placeholder' => __('Search...', 'custom-plugin'),
      'button_text' => __('Search', 'custom-plugin')
    ), $atts, 'header-search');

    ob_start();
  ?>
    <div class="custom-plugin-header-search">
      <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="text" name="s" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" />
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search-icon lucide-search">
          <path d="m21 21-4.34-4.34" />
          <circle cx="11" cy="11" r="8" />
        </svg>
      </form>
    </div>
  <?php
    return ob_get_clean();
  }

  /**
   * Custom form shortcode
   * Usage: [custom_form]
   */
  public function custom_form_shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'title' => __('Contact Form', 'custom-plugin'),
      'action' => '',
      'method' => 'post'
    ), $atts, 'custom_form');

    ob_start();
  ?>
    <div class="custom-plugin-form">
      <h3><?php echo esc_html($atts['title']); ?></h3>
      <form action="<?php echo esc_url($atts['action']); ?>" method="<?php echo esc_attr($atts['method']); ?>">
        <p>
          <label for="name"><?php _e('Name:', 'custom-plugin'); ?></label>
          <input type="text" id="name" name="name" required>
        </p>
        <p>
          <label for="email"><?php _e('Email:', 'custom-plugin'); ?></label>
          <input type="email" id="email" name="email" required>
        </p>
        <p>
          <label for="message"><?php _e('Message:', 'custom-plugin'); ?></label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </p>
        <p>
          <input type="submit" value="<?php _e('Send', 'custom-plugin'); ?>">
        </p>
      </form>
    </div>
  <?php
    return ob_get_clean();
  }

  /**
   * Custom message shortcode
   * Usage: [custom_message type="success" message="Your message"]
   */
  public function custom_message_shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'type' => 'info',
      'message' => __('Default message', 'custom-plugin'),
      'dismissible' => 'false'
    ), $atts, 'custom_message');

    $classes = array('custom-plugin-message', 'message-' . esc_attr($atts['type']));

    if ($atts['dismissible'] === 'true') {
      $classes[] = 'dismissible';
    }

    ob_start();
  ?>
    <div class="<?php echo implode(' ', $classes); ?>">
      <p><?php echo wp_kses_post($atts['message']); ?></p>
      <?php if ($atts['dismissible'] === 'true'): ?>
        <button type="button" class="dismiss-btn">&times;</button>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * Custom data shortcode
   * Usage: [custom_data type="products" limit="5"]
   */
  public function custom_data_shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'type' => 'products',
      'limit' => 5,
      'orderby' => 'date',
      'order' => 'DESC'
    ), $atts, 'custom_data');

    $limit = intval($atts['limit']);

    ob_start();

    if ($atts['type'] === 'products') {
      $products = get_posts(array(
        'post_type' => 'custom_product',
        'posts_per_page' => $limit,
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'post_status' => 'publish'
      ));

      if ($products):
    ?>
        <div class="custom-plugin-products">
          <h3><?php _e('Latest Products', 'custom-plugin'); ?></h3>
          <div class="products-grid">
            <?php foreach ($products as $product):
              $price = get_post_meta($product->ID, '_custom_product_harga', true);
            ?>
              <div class="product-item">
                <?php if (has_post_thumbnail($product->ID)): ?>
                  <div class="product-image">
                    <?php echo get_the_post_thumbnail($product->ID, 'medium'); ?>
                  </div>
                <?php endif; ?>
                <div class="product-content">
                  <h4><a href="<?php echo get_permalink($product->ID); ?>"><?php echo esc_html($product->post_title); ?></a></h4>
                  <?php
                  // Display categories
                  $categories = get_the_terms($product->ID, 'category_product');
                  if ($categories && !is_wp_error($categories)): ?>
                    <p class="product-categories">
                      <?php foreach ($categories as $category): ?>
                        <span class="category-tag"><?php echo esc_html($category->name); ?></span>
                      <?php endforeach; ?>
                    </p>
                  <?php endif; ?>
                  <?php if ($price): ?>
                    <p class="product-price">Rp <?php echo number_format($price, 0, ',', '.'); ?></p>
                  <?php endif; ?>
                  <div class="product-excerpt">
                    <?php echo wp_trim_words($product->post_content, 20); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php
      else:
        echo '<p>' . __('No products found.', 'custom-plugin') . '</p>';
      endif;
    } elseif ($atts['type'] === 'orders') {
      $orders = get_posts(array(
        'post_type' => 'custom_order',
        'posts_per_page' => $limit,
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'post_status' => 'publish'
      ));

      if ($orders):
      ?>
        <div class="custom-plugin-orders">
          <h3><?php _e('Recent Orders', 'custom-plugin'); ?></h3>
          <div class="orders-list">
            <?php foreach ($orders as $order):
              $status = get_post_meta($order->ID, '_custom_order_status', true);
              $total = get_post_meta($order->ID, '_custom_order_total_harga', true);
            ?>
              <div class="order-item">
                <div class="order-header">
                  <h4><?php echo esc_html($order->post_title); ?></h4>
                  <span class="order-status status-<?php echo esc_attr($status); ?>">
                    <?php echo esc_html(ucfirst($status)); ?>
                  </span>
                </div>
                <?php if ($total): ?>
                  <p class="order-total">Total: Rp <?php echo number_format($total, 0, ',', '.'); ?></p>
                <?php endif; ?>
                <p class="order-date"><?php echo get_the_date('', $order->ID); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
    <?php
      else:
        echo '<p>' . __('No orders found.', 'custom-plugin') . '</p>';
      endif;
    }

    return ob_get_clean();
  }

  /**
   * Render categories with images shortcode
   * Uses Categories Images plugin to display category images
   */
  public function render_categories_with_images($atts)
  {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
      'taxonomy' => 'category_product',
      'show_count' => 'no',
      'show_description' => 'yes',
      'hide_empty' => 'yes',
      'orderby' => 'name',
      'order' => 'ASC',
      'limit' => 0,
      'class' => '',
      'style' => 'grid', // grid, list
      'image_size' => 'medium',
      'columns' => 3
    ), $atts, 'categories_with_images');

    // Get categories
    $args = array(
      'taxonomy' => $atts['taxonomy'],
      'orderby' => $atts['orderby'],
      'order' => $atts['order'],
      'hide_empty' => $atts['hide_empty'] === 'yes',
      'number' => $atts['limit'] > 0 ? $atts['limit'] : '',
    );

    $categories = get_terms($args);

    if (empty($categories) || is_wp_error($categories)) {
      return '<p>' . __('No categories found.', 'custom-plugin') . '</p>';
    }

    // Start output buffering
    ob_start();

    $container_class = 'categories-with-images ' . $atts['style'] . '-style';
    if (!empty($atts['class'])) {
      $container_class .= ' ' . esc_attr($atts['class']);
    }
    if ($atts['style'] === 'grid') {
      $container_class .= ' columns-' . intval($atts['columns']);
    }
    ?>
    <div class="<?php echo esc_attr($container_class); ?> row">
      <?php foreach ($categories as $category):
        // Get category image using Categories Images plugin
        $image_id = '';
        $image_url = '';

        // Check if Categories Images plugin functions exist
        if (function_exists('z_taxonomy_image_url')) {
          $image_url = z_taxonomy_image_url($category->term_id, $atts['image_size']);
        } elseif (function_exists('get_term_meta')) {
          // Alternative method - check for common meta keys used by category image plugins
          $image_id = get_term_meta($category->term_id, 'category-image-id', true);
          if (!$image_id) {
            $image_id = get_term_meta($category->term_id, 'thumbnail_id', true);
          }
          if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, $atts['image_size']);
          }
        }

        $category_link = get_term_link($category);
      ?>
        <div class="category-item col-md-4">
          <a href="<?php echo esc_url($category_link); ?>" class="category-link">
            <?php if ($image_url): ?>
              <div class="category-image">
                <img src="<?php echo esc_url($image_url); ?>"
                  alt="<?php echo esc_attr($category->name); ?>"
                  loading="lazy">
              </div>
            <?php else: ?>
              <div class="category-image placeholder">
                <div class="placeholder-icon">
                  <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <polyline points="21,15 16,10 5,21" />
                  </svg>
                </div>
              </div>
            <?php endif; ?>

            <div class="category-content">
              <h3 class="category-title"><?php echo esc_html($category->name); ?></h3>

              <?php if ($atts['show_description'] === 'yes' && !empty($category->description)): ?>
                <p class="category-description"><?php echo esc_html($category->description); ?></p>
              <?php endif; ?>

              <?php if ($atts['show_count'] === 'yes'): ?>
                <span class="category-count">
                  <?php
                  printf(
                    _n('%d item', '%d items', $category->count, 'custom-plugin'),
                    $category->count
                  );
                  ?>
                </span>
              <?php endif; ?>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
<?php

    return ob_get_clean();
  }
}
