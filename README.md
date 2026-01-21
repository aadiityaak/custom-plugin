# Custom Plugin

Plugin WordPress kustom yang menyediakan fitur-fitur core tambahan dan informasi sistem untuk mempercepat pengembangan website.

## Deskripsi

Custom Plugin dirancang sebagai fondasi untuk pengembangan website WordPress kustom. Plugin ini menyediakan registrasi Custom Post Types, Taxonomies, dukungan fitur core (seperti upload SVG), dan halaman informasi sistem yang komprehensif.

## Fitur Utama

- �️ **Dashboard Info Sistem**: Menampilkan informasi detail tentang server, PHP, WordPress, dan database.
- 🚀 **Core Features**:
  - Registrasi Custom Post Types (mudah dikonfigurasi).
  - Registrasi Custom Taxonomies.
  - Dukungan upload file SVG.
  - Pengaturan ukuran gambar (Image Sizes).
- ⚙️ **Pengaturan**: Konfigurasi fitur melalui halaman settings.
- � **Struktur Modular**: Menggunakan namespace dan autoloading untuk pengelolaan kode yang lebih baik.

## Fitur (Non-Aktif Sementara)

Fitur berikut tersedia namun saat ini dinonaktifkan secara default:

- 📝 **Form Kontak**: Menggunakan Alpine.js dan REST API.
- 📊 **Submissions**: Halaman admin untuk melihat data kiriman form.

## Instalasi

1. Download folder plugin.
2. Upload folder `custom-plugin` ke direktori `/wp-content/plugins/`.
3. Aktifkan plugin melalui menu 'Plugins' di WordPress admin.

## Penggunaan

### Dashboard

Akses menu **Custom Plugin** di sidebar admin untuk melihat informasi sistem (Versi PHP, Memory Limit, Info Database, dll).

### Pengaturan

Akses **Custom Plugin > Settings** untuk mengaktifkan/menonaktifkan fitur tertentu.

### Shortcodes

- `[custom_message]`: Menampilkan pesan kustom.
- `[custom_data]`: Menampilkan data custom.
- `[custom_form]`: (Non-aktif) Form kontak.

## Struktur File

```
custom-plugin/
├── custom-plugin.php          # Entry point
├── src/                       # Source code (Autoloaded)
│   ├── Admin/                 # Logika halaman Admin
│   ├── Api/                   # REST API Controller
│   ├── Core/                  # Fitur Inti (CPT, Taxonomies, dll)
│   ├── Frontend/              # Logika Frontend & Shortcode
│   └── Utils/                 # Utilitas
├── templates/                 # File View/Template
│   └── admin/                 # Template halaman admin
├── assets/                    # CSS & JS
└── languages/                 # File terjemahan
```

## Pengembangan

Untuk menambahkan Custom Post Type atau Taxonomy baru, Anda dapat memodifikasi file di dalam folder `src/Core/`:

- `src/Core/PostTypes.php`
- `src/Core/Taxonomies.php`
