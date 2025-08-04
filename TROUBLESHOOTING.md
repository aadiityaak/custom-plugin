# Troubleshooting: Meta Box Save Issues

## Masalah yang Ditemukan & Solusinya

### 1. **Debugging dan Logging**

Saya telah menambahkan logging ke dalam fungsi `save_meta_boxes()` untuk membantu debug masalah. Untuk melihat log:

1. Aktifkan WordPress debug dengan menambahkan ini ke `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

2. Periksa file log di: `/wp-content/debug.log`

### 2. **Kemungkinan Penyebab Masalah Save**

#### A. **Nonce Verification Gagal**

- Pastikan form meta box memiliki nonce field yang benar
- Check apakah nonce di-submit dengan form

#### B. **Post Type Tidak Sesuai**

- Pastikan Anda sedang edit post dengan type `custom_product` atau `custom_order`
- Bukan pada post type `post` atau `page` biasa

#### C. **Permission Issues**

- User harus memiliki capability `edit_post` untuk post tersebut

#### D. **JavaScript Conflicts**

- Mungkin ada conflict dengan theme atau plugin lain

### 3. **Langkah-langkah Debugging**

#### Step 1: Periksa Log Error

```bash
tail -f /path/to/wp-content/debug.log
```

Lalu coba save product dengan harga dan lihat log output.

#### Step 2: Manual Check

Tambahkan ini ke `functions.php` theme untuk test:

```php
add_action('save_post', function($post_id) {
    error_log('WordPress save_post hook called for: ' . $post_id);
    error_log('POST data: ' . print_r($_POST, true));
});
```

#### Step 3: Check Database Directly

```sql
SELECT * FROM wp_postmeta WHERE meta_key = '_custom_product_harga';
```

### 4. **Perbaikan yang Telah Dilakukan**

1. **Fixed Nonce Logic**: Nonce sekarang dicheck per post type, bukan global
2. **Added Debugging**: Log detail untuk tracking masalah
3. **Improved Validation**: Better validation dan error handling
4. **Enhanced UI**: Better form styling dan user experience

### 5. **Cara Test Plugin**

1. **Buat Product Baru**:

   - Go to: `wp-admin/post-new.php?post_type=custom_product`
   - Isi title dan content
   - Isi harga di meta box "Product Details"
   - Save post

2. **Buat Order Baru**:

   - Go to: `wp-admin/post-new.php?post_type=custom_order`
   - Isi title dan content
   - Pilih status di meta box "Order Details"
   - Save post

3. **Verify Save**:
   - Reload halaman edit
   - Check apakah nilai masih ada
   - Check database: `SELECT * FROM wp_postmeta WHERE post_id = [POST_ID]`

### 6. **Kemungkinan Solusi Lain**

#### A. Priority Hook Issue

Coba ubah priority save_post hook:

```php
add_action('save_post', array($this, 'save_meta_boxes'), 10, 1);
```

#### B. Alternative Hook

Gunakan hook yang lebih spesifik:

```php
add_action('save_post_custom_product', array($this, 'save_product_meta'), 10, 1);
add_action('save_post_custom_order', array($this, 'save_order_meta'), 10, 1);
```

#### C. Check WordPress Version

Pastikan menggunakan WordPress versi yang compatible (5.0+).

### 7. **File-file yang Diperbaiki**

- ✅ `class-meta-box.php` - Logic save diperbaiki
- ✅ `meta-box.css` - Styling ditambahkan
- ✅ `meta-box.js` - Validation dan UX ditambahkan
- ✅ `custom-plugin.php` - CSS/JS meta box dienqueue

### 8. **Quick Fix Test**

Jika masih tidak berfungsi, coba test simple ini di `class-meta-box.php`:

```php
public function save_meta_boxes($post_id) {
    // Simple test - save langsung tanpa validation
    if (isset($_POST['custom_product_harga'])) {
        update_post_meta($post_id, '_custom_product_harga', $_POST['custom_product_harga']);
        error_log('QUICK FIX: Saved harga ' . $_POST['custom_product_harga'] . ' for post ' . $post_id);
    }
}
```

Jika ini berfungsi, berarti masalah ada di validation logic.

---

**Langkah Selanjutnya:**

1. Aktifkan debug logging
2. Test save product dengan harga
3. Periksa log error
4. Berikan feedback hasil log untuk analisis lebih lanjut
