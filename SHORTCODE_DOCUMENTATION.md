# Dokumentasi Shortcode Custom Plugin

## Shortcode Form Pemesanan

### Penggunaan Dasar

Untuk menampilkan form pemesanan di halaman atau post, gunakan shortcode berikut:

```
[custom_order_form]
```

### Parameter Shortcode

Shortcode `custom_order_form` mendukung beberapa parameter untuk kustomisasi:

#### 1. Product ID

Menentukan produk tertentu yang akan dipesan:

```
[custom_order_form product_id="123"]
```

#### 2. Show Product Select

Menampilkan atau menyembunyikan dropdown pilihan produk:

```
[custom_order_form show_product_select="no"]
```

#### 3. Title

Mengubah judul form:

```
[custom_order_form title="Pesan Sekarang"]
```

#### 4. Submit Text

Mengubah teks tombol submit:

```
[custom_order_form submit_text="Kirim Pesanan"]
```

#### 5. CSS Class

Menambahkan class CSS kustom:

```
[custom_order_form class="my-custom-form"]
```

### Contoh Penggunaan Lengkap

```
[custom_order_form
    product_id="123"
    show_product_select="no"
    title="Form Pemesanan Khusus"
    submit_text="Buat Pesanan Sekarang"
    class="premium-form"]
```

### Auto-Detection di Single Product Page

Ketika shortcode ditempatkan di halaman single product (`custom_product`), shortcode akan otomatis:

- Mengambil ID produk dari halaman tersebut
- Menyembunyikan dropdown pilihan produk
- Menampilkan detail produk yang sedang dilihat

### Fitur Form Pemesanan

#### 1. Informasi Produk

- Pilihan produk (jika tidak di single product page)
- Jumlah pesanan
- Kalkulasi total harga otomatis

#### 2. Informasi Pemesan

- Nama lengkap (wajib)
- Email (wajib)
- No. telepon (wajib)
- Alamat lengkap (wajib)

#### 3. Catatan Tambahan

- Catatan atau permintaan khusus (opsional)

#### 4. Validasi Real-time

- Validasi field wajib
- Validasi format email
- Validasi format nomor telepon Indonesia
- Feedback visual untuk field yang valid/invalid

#### 5. AJAX Submission

- Submit form tanpa refresh halaman
- Loading state dengan spinner
- Pesan sukses/error yang jelas
- Auto-redirect ke halaman order (opsional)

### Shortcode Lainnya

#### 1. Header Search

Menampilkan form pencarian:

```
[header-search placeholder="Cari produk..."]
```

#### 2. Custom Form

Menampilkan form kontak sederhana:

```
[custom_form title="Hubungi Kami"]
```

#### 3. Custom Message

Menampilkan pesan dengan styling:

```
[custom_message type="success" message="Pesanan berhasil!" dismissible="true"]
```

Tipe pesan: `info`, `success`, `warning`, `error`

#### 4. Data Display

Menampilkan daftar produk atau pesanan:

```
[custom_data type="products" limit="6" orderby="date" order="DESC"]
[custom_data type="orders" limit="5"]
```

### Styling dan Customization

#### CSS Classes Available

- `.custom-order-form-container` - Container utama form
- `.custom-order-form` - Form wrapper
- `.form-section` - Section dalam form
- `.form-group` - Group input
- `.submit-order-btn` - Tombol submit
- `.success-message` - Pesan sukses
- `.error-message` - Pesan error

#### Responsive Design

Form sudah responsive dan akan menyesuaikan dengan ukuran layar:

- Desktop: Layout 2 kolom untuk beberapa field
- Tablet: Layout single kolom dengan spacing optimal
- Mobile: Layout compact dengan button full-width

### JavaScript Events

Plugin menyediakan beberapa JavaScript events untuk customization:

```javascript
// Event setelah form berhasil disubmit
$(document).on("orderSubmitSuccess", function (e, data) {
  console.log("Order created:", data);
});

// Event ketika terjadi error
$(document).on("orderSubmitError", function (e, error) {
  console.log("Order error:", error);
});
```

### Troubleshooting

#### Form tidak muncul

1. Pastikan plugin aktif
2. Cek file CSS dan JS ter-load
3. Periksa console browser untuk error

#### AJAX tidak bekerja

1. Pastikan WordPress AJAX URL tersedia
2. Cek nonce verification
3. Periksa permissions dan capabilities

#### Styling tidak sesuai

1. Pastikan file CSS ter-load
2. Cek conflicts dengan theme CSS
3. Gunakan CSS specificity yang lebih tinggi

### Integration dengan Theme

Untuk integrasi optimal dengan theme:

1. Tambahkan CSS kustom di theme:

```css
.custom-order-form-container {
  /* Override plugin styles */
}
```

2. Enqueue script tambahan:

```php
wp_enqueue_script('theme-order-form', get_template_directory_uri() . '/js/order-form.js', array('custom-plugin-shortcode'), '1.0', true);
```

3. Hook ke WordPress actions:

```php
// Setelah order dibuat
add_action('custom_plugin_order_created', 'my_custom_order_handler');
function my_custom_order_handler($order_id) {
    // Custom logic
}
```
