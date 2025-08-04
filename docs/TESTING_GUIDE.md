# Testing Order Tracking System

## Setup untuk Testing

1. **Aktifkan Plugin**

   - Pastikan plugin `custom-plugin` sudah aktif di WordPress admin

2. **Buat Produk Test**

   - Masuk ke `Custom Plugin > Products > Add New`
   - Buat produk dengan nama dan harga
   - Publish produk

3. **Buat Order Test**

   - Gunakan shortcode `[custom_order_form]` di halaman
   - Isi form dan submit untuk membuat order dengan invoice ID (misal: INV-2024-001)

4. **Update Timeline Order (Opsional)**

   - Masuk ke `Custom Plugin > Orders`
   - Edit order yang baru dibuat
   - Isi timeline steps di meta box "Timeline Pengerjaan"

5. **Test Tracking**
   - Buat halaman baru dengan shortcode `[order_tracking]`
   - Masukkan invoice number yang sudah dibuat
   - Klik "Lacak Pesanan"

## Contoh Invoice Numbers untuk Testing

Jika belum ada order, bisa create manual:

- INV-2024-001
- INV-2024-002
- INV-2024-003

## Troubleshooting

### Error "Pesanan tidak ditemukan"

- Pastikan invoice number benar (case sensitive)
- Pastikan order sudah di-publish (status = publish)
- Check di database wp_posts bahwa post_title = invoice number

### Error AJAX/JavaScript

- Check browser console untuk error details
- Pastikan jQuery loaded
- Pastikan nonce name sesuai antara PHP dan JS

### Timeline tidak muncul

- Pastikan timeline data tersimpan di post meta `_custom_order_timeline`
- Check format array timeline sesuai dengan yang diexpect

## Test Cases

1. **Happy Path**

   - Input valid invoice → Should show order details + timeline

2. **Error Cases**

   - Input kosong → "Mohon masukkan nomor invoice"
   - Invoice tidak ada → "Pesanan tidak ditemukan"
   - Server error → "Terjadi kesalahan saat mencari pesanan"

3. **Timeline Scenarios**
   - Order tanpa timeline → Show basic info only
   - Order dengan partial timeline → Show completed + pending steps
   - Order dengan delivery info → Show driver name + phone
   - Order dengan proof photo → Show delivery image

## File Locations

- **Frontend Form**: `/wp-content/plugins/custom-plugin/includes/class-shortcodes.php`
- **CSS Styling**: `/wp-content/plugins/custom-plugin/assets/css/shortcode.css`
- **JavaScript**: `/wp-content/plugins/custom-plugin/assets/js/tracking.js`
- **Main Plugin**: `/wp-content/plugins/custom-plugin/custom-plugin.php`
