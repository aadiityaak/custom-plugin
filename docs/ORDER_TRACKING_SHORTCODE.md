# Custom Plugin - Order Tracking Shortcode

## Shortcode: [order_tracking]

Shortcode ini digunakan untuk menampilkan form pencarian dan hasil tracking pesanan berdasarkan nomor invoice.

### Cara Penggunaan

```php
[order_tracking]
```

### Fitur

1. **Form Pencarian Invoice**

   - Input field untuk memasukkan nomor invoice
   - Tombol "Lacak Pesanan"
   - Validasi input (tidak boleh kosong)
   - Auto-uppercase untuk input invoice

2. **Tampilan Hasil Tracking**

   - Informasi order (invoice, status, customer, produk, tanggal, total)
   - Timeline pengerjaan dengan 11 tahap
   - Informasi driver pengiriman
   - Bukti pengiriman (foto)

3. **Status Badge**
   - Pending (Menunggu)
   - Processing (Diproses)
   - Shipped (Dikirim)
   - Delivered (Terkirim)
   - Cancelled (Dibatalkan)

### Timeline Tahapan

1. Pesanan Diterima
2. Konfirmasi Pembayaran
3. Persiapan Produk
4. Proses Produksi
5. Quality Control
6. Packaging
7. Siap Kirim
8. Pengiriman
9. Dalam Perjalanan
10. Tiba di Tujuan
11. Diterima Customer

### Styling

Menggunakan desain minimalis expedition-style dengan:

- Warna dasar putih dan abu-abu
- Tanpa border-radius (flat design)
- Timeline dengan marker hijau untuk tahap completed
- Responsive design untuk mobile
- Grid layout untuk informasi order

### AJAX Integration

- Menggunakan WordPress AJAX untuk pencarian real-time
- Nonce security untuk validasi request
- Loading state pada tombol submit
- Error handling untuk pesanan tidak ditemukan

### File yang Terlibat

1. `class-shortcodes.php` - Logic shortcode dan AJAX handler
2. `shortcode.css` - Styling untuk tampilan tracking
3. `tracking.js` - JavaScript untuk form submission dan hasil display
4. `custom-plugin.php` - Enqueue assets dan localization

### Contoh Penggunaan di Halaman/Post

```html
<h2>Lacak Pesanan Anda</h2>
<p>Masukkan nomor invoice untuk melacak status pesanan Anda:</p>

[order_tracking]

<p><small>Contoh format invoice: INV-2024-001</small></p>
```

### Customization

Untuk menyesuaikan tampilan, edit file:

- `assets/css/shortcode.css` - untuk styling
- `assets/js/tracking.js` - untuk behavior JavaScript
- `includes/class-shortcodes.php` - untuk logic backend
