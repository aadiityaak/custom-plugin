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

    // Get order details
    $jumlah = get_post_meta($post->ID, '_custom_order_jumlah', true);
    $total_harga = get_post_meta($post->ID, '_custom_order_total_harga', true);
    $nama_pemesan = get_post_meta($post->ID, '_custom_order_nama_pemesan', true);
    $alamat = get_post_meta($post->ID, '_custom_order_alamat', true);
    $no_hp = get_post_meta($post->ID, '_custom_order_no_hp', true);

    // Get timeline data
    $timeline = get_post_meta($post->ID, '_custom_order_timeline', true);
    $timeline = $timeline ? $timeline : array();

    // Get delivery info
    $delivery_info = get_post_meta($post->ID, '_custom_order_delivery_info', true);

    // Define status options
    $status_options = array(
      'pending' => __('Pending', 'custom-plugin'),
      'processing' => __('Processing', 'custom-plugin'),
      'shipped' => __('Shipped', 'custom-plugin'),
      'delivered' => __('Delivered', 'custom-plugin'),
      'cancelled' => __('Cancelled', 'custom-plugin')
    );

    // Timeline steps
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

    // Render the meta box content
    echo '<div class="custom-plugin-meta-box order-meta-box">';

    // Order Basic Information
    echo '<h3>' . __('Informasi Order', 'custom-plugin') . '</h3>';
    echo '<table class="form-table">';

    // Order ID/Invoice ID (using post title)
    echo '<tr>';
    echo '<th><label for="custom_order_invoice_id">' . __('Order ID/Invoice:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<input type="text" id="custom_order_invoice_id" name="post_title" value="' . esc_attr($post->post_title) . '" class="regular-text" placeholder="INV-2024-001" />';
    echo '<p class="description">' . __('ID Order/Invoice (akan menjadi judul post)', 'custom-plugin') . '</p>';
    echo '</td>';
    echo '</tr>';

    // Jumlah
    echo '<tr>';
    echo '<th><label for="custom_order_jumlah">' . __('Jumlah:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<input type="number" id="custom_order_jumlah" name="custom_order_jumlah" value="' . esc_attr($jumlah) . '" class="regular-text" min="1" />';
    echo '<p class="description">' . __('Jumlah item yang dipesan', 'custom-plugin') . '</p>';
    echo '</td>';
    echo '</tr>';

    // Total Harga
    echo '<tr>';
    echo '<th><label for="custom_order_total_harga">' . __('Total Harga:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<div class="price-input">';
    echo '<input type="number" id="custom_order_total_harga" name="custom_order_total_harga" value="' . esc_attr($total_harga) . '" class="regular-text" step="0.01" min="0" placeholder="0.00" />';
    echo '</div>';
    echo '<p class="description">' . __('Total harga dalam rupiah', 'custom-plugin') . '</p>';
    echo '</td>';
    echo '</tr>';

    // Status
    echo '<tr>';
    echo '<th><label for="custom_order_status">' . __('Status:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<select id="custom_order_status" name="custom_order_status" class="regular-text">';
    foreach ($status_options as $value => $label) {
      echo '<option value="' . esc_attr($value) . '"' . selected($status, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">' . __('Status order saat ini', 'custom-plugin') . '</p>';
    echo '</td>';
    echo '</tr>';

    echo '</table>';

    // Customer Information
    echo '<h3>' . __('Informasi Pemesan', 'custom-plugin') . '</h3>';
    echo '<table class="form-table">';

    // Nama Pemesan
    echo '<tr>';
    echo '<th><label for="custom_order_nama_pemesan">' . __('Nama Pemesan:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<input type="text" id="custom_order_nama_pemesan" name="custom_order_nama_pemesan" value="' . esc_attr($nama_pemesan) . '" class="regular-text" />';
    echo '</td>';
    echo '</tr>';

    // Alamat
    echo '<tr>';
    echo '<th><label for="custom_order_alamat">' . __('Alamat:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<textarea id="custom_order_alamat" name="custom_order_alamat" rows="3" class="large-text">' . esc_textarea($alamat) . '</textarea>';
    echo '</td>';
    echo '</tr>';

    // No HP
    echo '<tr>';
    echo '<th><label for="custom_order_no_hp">' . __('No. HP:', 'custom-plugin') . '</label></th>';
    echo '<td>';
    echo '<input type="tel" id="custom_order_no_hp" name="custom_order_no_hp" value="' . esc_attr($no_hp) . '" class="regular-text" placeholder="08xxxxxxxxxx" />';
    echo '</td>';
    echo '</tr>';

    echo '</table>';

    // Timeline Progress
    echo '<h3>' . __('Timeline Pengerjaan', 'custom-plugin') . '</h3>';
    echo '<div class="timeline-container">';

    foreach ($timeline_steps as $step_num => $step_label) {
      $step_date = isset($timeline[$step_num]['date']) ? $timeline[$step_num]['date'] : '';
      $step_time = isset($timeline[$step_num]['time']) ? $timeline[$step_num]['time'] : '';
      $is_completed = !empty($step_date) && !empty($step_time);

      echo '<div class="timeline-step ' . ($is_completed ? 'completed' : '') . '" data-step="' . $step_num . '">';
      echo '<div class="step-header">';
      echo '<h4><span class="step-number">' . $step_num . ' </span>' . $step_label . '</h4>';
      echo '</div>';

      echo '<div class="step-datetime">';
      echo '<label>' . __('Tanggal:', 'custom-plugin') . '</label>';
      echo '<input type="date" name="timeline_step_' . $step_num . '_date" value="' . esc_attr($step_date) . '" />';
      echo '<label>' . __('Waktu:', 'custom-plugin') . '</label>';
      echo '<input type="time" name="timeline_step_' . $step_num . '_time" value="' . esc_attr($step_time) . '" />';
      echo '</div>';

      // Special field for delivery step (step 10)
      if ($step_num == 10) {
        $driver_name = isset($timeline[$step_num]['driver_name']) ? $timeline[$step_num]['driver_name'] : '';
        $driver_phone = isset($timeline[$step_num]['driver_phone']) ? $timeline[$step_num]['driver_phone'] : '';

        echo '<div class="delivery-info">';
        echo '<label>' . __('Nama Driver:', 'custom-plugin') . '</label>';
        echo '<input type="text" name="timeline_step_' . $step_num . '_driver_name" value="' . esc_attr($driver_name) . '" placeholder="Nama lengkap driver" />';
        echo '<label>' . __('No. Telp Driver:', 'custom-plugin') . '</label>';
        echo '<input type="tel" name="timeline_step_' . $step_num . '_driver_phone" value="' . esc_attr($driver_phone) . '" placeholder="08xxxxxxxxxx" />';
        echo '</div>';
      }

      // Special field for received step (step 11)
      if ($step_num == 11) {
        $photo_proof = isset($timeline[$step_num]['photo_proof']) ? $timeline[$step_num]['photo_proof'] : '';

        echo '<div class="photo-proof">';
        echo '<label>' . __('Bukti Foto:', 'custom-plugin') . '</label>';
        echo '<input type="hidden" name="timeline_step_' . $step_num . '_photo_proof" value="' . esc_attr($photo_proof) . '" />';
        echo '<button type="button" class="button upload-photo-btn" data-step="' . $step_num . '">' . __('Upload Foto', 'custom-plugin') . '</button>';
        if ($photo_proof) {
          echo '<div class="photo-preview"><img src="' . wp_get_attachment_url($photo_proof) . '" style="max-width: 150px; height: auto;" /></div>';
        }
        echo '<p class="description">' . __('Upload bukti foto penerimaan barang', 'custom-plugin') . '</p>';
        echo '</div>';
      }

      echo '</div>';
    }

    echo '</div>';
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

        // Save basic order info
        if (isset($_POST['custom_order_status'])) {
          $status = sanitize_text_field($_POST['custom_order_status']);
          update_post_meta($post_id, '_custom_order_status', $status);
          error_log('Custom Plugin: Saving order status: ' . $status);
        }

        if (isset($_POST['custom_order_jumlah'])) {
          $jumlah = intval($_POST['custom_order_jumlah']);
          update_post_meta($post_id, '_custom_order_jumlah', $jumlah);
        }

        if (isset($_POST['custom_order_total_harga'])) {
          $total_harga = sanitize_text_field($_POST['custom_order_total_harga']);
          update_post_meta($post_id, '_custom_order_total_harga', $total_harga);
        }

        if (isset($_POST['custom_order_nama_pemesan'])) {
          $nama_pemesan = sanitize_text_field($_POST['custom_order_nama_pemesan']);
          update_post_meta($post_id, '_custom_order_nama_pemesan', $nama_pemesan);
        }

        if (isset($_POST['custom_order_alamat'])) {
          $alamat = sanitize_textarea_field($_POST['custom_order_alamat']);
          update_post_meta($post_id, '_custom_order_alamat', $alamat);
        }

        if (isset($_POST['custom_order_no_hp'])) {
          $no_hp = sanitize_text_field($_POST['custom_order_no_hp']);
          update_post_meta($post_id, '_custom_order_no_hp', $no_hp);
        }

        // Save timeline data
        $timeline_data = array();
        for ($i = 1; $i <= 11; $i++) {
          $step_date = isset($_POST['timeline_step_' . $i . '_date']) ? sanitize_text_field($_POST['timeline_step_' . $i . '_date']) : '';
          $step_time = isset($_POST['timeline_step_' . $i . '_time']) ? sanitize_text_field($_POST['timeline_step_' . $i . '_time']) : '';

          // Only save if both date and time are provided
          if (!empty($step_date) && !empty($step_time)) {
            $timeline_data[$i] = array(
              'date' => $step_date,
              'time' => $step_time,
              'completed' => true
            );

            // Special data for delivery step (step 10)
            if ($i == 10) {
              if (isset($_POST['timeline_step_' . $i . '_driver_name'])) {
                $timeline_data[$i]['driver_name'] = sanitize_text_field($_POST['timeline_step_' . $i . '_driver_name']);
              }
              if (isset($_POST['timeline_step_' . $i . '_driver_phone'])) {
                $timeline_data[$i]['driver_phone'] = sanitize_text_field($_POST['timeline_step_' . $i . '_driver_phone']);
              }
            }

            // Special data for received step (step 11)
            if ($i == 11) {
              if (isset($_POST['timeline_step_' . $i . '_photo_proof'])) {
                $timeline_data[$i]['photo_proof'] = intval($_POST['timeline_step_' . $i . '_photo_proof']);
              }
            }
          }
        }

        update_post_meta($post_id, '_custom_order_timeline', $timeline_data);
        error_log('Custom Plugin: Timeline data saved');
      } else {
        error_log('Custom Plugin: Order nonce verification failed');
      }
    }
  }
}

// Initialize the meta box class
new CustomPluginMetaBox();
