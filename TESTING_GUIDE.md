# Custom Plugin - Test Instructions

## Testing Shortcode Form Pemesanan

### 1. Aktivasi Plugin

1. Upload plugin ke folder `wp-content/plugins/custom-plugin/`
2. Aktifkan plugin di WordPress Admin > Plugins
3. Pastikan Custom Post Types (Products & Orders) muncul di admin menu

### 2. Buat Produk Sample

1. Buka Admin > Products > Add New
2. Isi:
   - Title: "Contoh Produk 1"
   - Content: "Deskripsi produk..."
   - Harga: 50000
   - Featured Image: Upload gambar
3. Publish produk
4. Ulangi untuk beberapa produk

### 3. Test Shortcode di Halaman

1. Buat halaman baru atau edit halaman existing
2. Tambahkan shortcode: `[custom_order_form]`
3. Publish/Update halaman
4. Buka halaman di frontend

### 4. Test Form Pemesanan

1. Isi semua field yang required:
   - Pilih produk
   - Masukkan jumlah
   - Isi nama lengkap
   - Isi email valid
   - Isi nomor telepon (format: 08xxxxxxxxxx)
   - Isi alamat lengkap
2. Submit form
3. Cek apakah order baru muncul di Admin > Orders

### 5. Test di Single Product Page

1. Buka halaman single product (`/custom_product/nama-produk/`)
2. Tambahkan shortcode `[custom_order_form]` di content produk
3. Form akan otomatis menggunakan produk tersebut

### 6. Test Variasi Shortcode

Test shortcode dengan parameter:

```
[custom_order_form title="Pesan Sekarang" submit_text="Buat Pesanan"]
```

```
[custom_order_form product_id="123" show_product_select="no"]
```

### 7. Verifikasi Order Management

1. Buka Admin > Orders
2. Edit order yang baru dibuat
3. Cek Timeline Pengerjaan
4. Test update status timeline

### 8. Test Responsive Design

1. Buka form di mobile device
2. Pastikan layout responsive
3. Test submit form di mobile

### 9. Test Validasi

Test validasi form:

- Submit tanpa mengisi field required
- Masukkan email format salah
- Masukkan nomor telepon format salah
- Pastikan error message muncul

### 10. Test AJAX Functionality

1. Buka Developer Tools > Console
2. Submit form valid
3. Pastikan tidak ada error JavaScript
4. Pastikan form submit tanpa refresh halaman

## Expected Results

### ✅ Form Display

- Form muncul dengan styling yang bagus
- Dropdown produk terisi
- Field input responsive

### ✅ Form Validation

- Required field validation bekerja
- Email format validation
- Phone format validation
- Error messages jelas

### ✅ Form Submission

- Submit berhasil tanpa refresh
- Success message muncul
- Order baru dibuat di database
- Timeline initialized dengan step 1

### ✅ Order Management

- Order muncul di admin list
- Meta box timeline berfungsi
- Data customer tersimpan

### ✅ Responsive Design

- Mobile friendly
- Touch-friendly buttons
- Readable text size

## Troubleshooting

### Form tidak muncul

```php
// Cek di functions.php theme
add_action('wp_footer', function() {
    echo '<!-- Checking shortcode -->';
    if (shortcode_exists('custom_order_form')) {
        echo '<!-- Shortcode exists -->';
    } else {
        echo '<!-- Shortcode NOT exists -->';
    }
});
```

### CSS tidak load

```php
// Cek di Developer Tools > Network
// Pastikan file CSS ter-load:
// /wp-content/plugins/custom-plugin/assets/css/shortcode.css
```

### JavaScript error

```javascript
// Cek di Console untuk error:
// Pastikan jQuery loaded
// Pastikan customPluginAjax object tersedia
```

### AJAX tidak bekerja

```php
// Cek di wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Cek log file: /wp-content/debug.log
```

## Advanced Testing

### Custom CSS Override

```css
/* Tambahkan di theme style.css */
.custom-order-form-container {
  max-width: 600px;
  background: linear-gradient(45deg, #667eea, #764ba2);
}
```

### JavaScript Hook

```javascript
// Tambahkan custom handling
jQuery(document).ready(function ($) {
  $(document).on("orderSubmitSuccess", function (e, data) {
    alert("Order berhasil: " + data.invoice_id);
  });
});
```

### PHP Hook

```php
// Tambahkan di functions.php
add_action('custom_plugin_order_created', function($order_id) {
    // Send notification email
    wp_mail(
        get_option('admin_email'),
        'New Order Created',
        'Order ID: ' . $order_id
    );
});
```
