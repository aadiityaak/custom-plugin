# Custom Plugin

Plugin WordPress kustom yang menyediakan fitur-fitur tambahan untuk website Anda.

## Deskripsi

Custom Plugin adalah plugin WordPress yang dirancang untuk menambahkan fungsionalitas khusus ke website Anda. Plugin ini menyediakan form kontak sederhana, sistem manajemen data, dan berbagai shortcode yang berguna.

## Fitur Utama

- 📝 **Form Kontak**: Form kontak dengan validasi dan penyimpanan data
- 🎨 **Shortcodes**: Berbagai shortcode untuk menampilkan konten dinamis
- ⚙️ **Panel Admin**: Interface admin yang user-friendly
- 🔧 **Pengaturan Fleksibel**: Konfigurasi plugin sesuai kebutuhan
- 📊 **Manajemen Data**: Lihat dan kelola data yang dikirim melalui form
- 🌍 **Siap Terjemahan**: Mendukung internationalization (i18n)

## Instalasi

1. Download file plugin
2. Upload folder `custom-plugin` ke direktori `/wp-content/plugins/`
3. Aktifkan plugin melalui menu 'Plugins' di WordPress admin
4. Konfigurasi plugin melalui menu 'Custom Plugin' di admin dashboard

## Penggunaan

### Shortcodes

Plugin menyediakan beberapa shortcode yang bisa digunakan:

#### 1. Form Kontak

```
[custom_form]
```

Menampilkan form kontak sederhana dengan validasi.

#### 2. Pesan Kustom

```
[custom_message text="Pesan Anda" style="default"]
```

Menampilkan pesan kustom dengan berbagai style:

- `default`: Style default
- `success`: Style hijau untuk pesan sukses
- `warning`: Style kuning untuk peringatan
- `error`: Style merah untuk error

#### 3. Data Submissions

```
[custom_data limit="5" orderby="created_at" order="DESC"]
```

Menampilkan data submissions dalam bentuk tabel.

### Pengaturan

Akses pengaturan melalui **Custom Plugin > Settings** di admin dashboard:

1. **Enable Feature 1**: Menambahkan meta tag kustom di head section
2. **Enable Feature 2**: Menambahkan pesan kustom di single posts
3. **Custom Message**: Pesan yang akan ditampilkan ketika Feature 2 aktif

## Struktur File

```
custom-plugin/
├── custom-plugin.php          # File utama plugin
├── admin/
│   ├── admin-page.php        # Template halaman admin utama
│   └── settings-page.php     # Template halaman pengaturan
├── includes/
│   ├── class-admin.php       # Class untuk fungsi admin
│   ├── class-frontend.php    # Class untuk fungsi frontend
│   └── class-shortcodes.php  # Class untuk shortcodes
├── assets/
│   ├── css/
│   │   ├── admin.css         # Style untuk admin
│   │   └── frontend.css      # Style untuk frontend
│   └── js/
│       ├── admin.js          # JavaScript untuk admin
│       └── frontend.js       # JavaScript untuk frontend
├── languages/                # File terjemahan
└── README.md                # Dokumentasi ini
```

## Tabel Database

Plugin membuat tabel `wp_custom_plugin_data` untuk menyimpan data form:

```sql
CREATE TABLE wp_custom_plugin_data (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name tinytext NOT NULL,
    email varchar(100) NOT NULL,
    message text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

## Hooks dan Filters

### Actions

- `custom_plugin_after_form_submit`: Dipanggil setelah form berhasil disubmit
- `custom_plugin_before_data_display`: Dipanggil sebelum menampilkan data

### Filters

- `custom_plugin_form_fields`: Filter untuk memodifikasi field form
- `custom_plugin_message_content`: Filter untuk memodifikasi konten pesan

## Keamanan

Plugin menggunakan berbagai metode keamanan WordPress:

- Nonce verification untuk form submission
- Data sanitization dan validation
- Prepared statements untuk database queries
- Capability checks untuk akses admin

## Persyaratan Sistem

- WordPress 5.0 atau lebih baru
- PHP 7.4 atau lebih baru
- MySQL 5.6 atau lebih baru

## Changelog

### 1.0.0

- Release pertama
- Form kontak dengan validasi
- Panel admin dengan dashboard
- Shortcodes untuk menampilkan data
- Sistem pengaturan plugin

## Dukungan

Untuk dukungan dan pertanyaan, silakan hubungi:

- Email: support@yourwebsite.com
- Website: https://yourwebsite.com/support

## Lisensi

Plugin ini dirilis di bawah lisensi GPL v2 atau yang lebih baru.

## Kontribusi

Kontribusi sangat diterima! Silakan buat pull request atau laporkan bug melalui issue tracker.

## Author

Dikembangkan oleh Your Name

- Website: https://yourwebsite.com
- Email: your.email@domain.com
