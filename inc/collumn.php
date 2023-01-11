<?php

/**
 * Velocity Main Site Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Custom Plugin
 * @since 1.0.0
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

//KOLOM project
// Add the custom columns to the book post type:
add_filter('manage_project_posts_columns', 'set_custom_edit_project_columns');
function set_custom_edit_project_columns($columns)
{
    unset($columns['author']);
    $columns['penanggung_jawab'] = __('Penanggung Jawab', 'your_text_domain');
    $columns['alamat'] = __('Alamat', 'your_text_domain');
    $columns['nilai_kontrak'] = __('Laba / Rugi', 'your_text_domain');
    $columns['nama_pembeli'] = __('Nama Pembeli', 'your_text_domain');

    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_project_posts_custom_column', 'custom_project_column', 10, 2);
function custom_project_column($column, $post_id)
{
    switch ($column) {
        case 'penanggung_jawab':
            $penanggung_jawab_id = get_post_meta($post_id, 'penanggung_jawab', true);
            $penanggung_jawab_name = get_the_title($penanggung_jawab_id);
            echo $penanggung_jawab_name;
            $no_whatsapp = get_post_meta($post_id, 'no_telephone', true);
            echo $no_whatsapp;
            break;

        case 'alamat':
            echo get_post_meta($post_id, 'alamat', true);
            break;

        case 'nilai_kontrak':
?>
            <?php add_thickbox();
            $total = total_keuangan($post_id, 'debit', false) - total_keuangan($post_id, 'kredit', false);
            echo 'Rp ' . number_format($total, 2, ',', '.') . '<br>';
            ?>

            <a href="#TB_inline?width=600&height=350&inlineId=laporan-<?php echo $post_id; ?>" class="thickbox">Detail laporan</a>
            <div id="laporan-<?php echo $post_id; ?>" style="display:none;">
                <h3>
                    Laporan laba Rugi Project <?php echo get_the_title($post_id); ?>
                </h3>
                <div>
                    <?php
                    $add_debit = get_site_url() . '/wp-admin/post-new.php?post_type=keuangan&tipe=debit&project_id=' . $post_id;
                    $add_debit = '<a href="' . $add_debit . '">
                    <svg style="margin-bottom:-2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    </a>';
                    $add_kredit = get_site_url() . '/wp-admin/post-new.php?post_type=keuangan&tipe=kredit&project_id=' . $post_id;
                    $add_kredit = '<a href="' . $add_kredit . '">
                    <svg style="margin-bottom:-2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    </a>';
                    echo '<h3>Debit ' . $add_debit . '</h3>';
                    echo '<div style="margin-bottom:20px;">';
                    echo total_keuangan($post_id, 'debit', true);
                    echo '</div>';

                    echo '<h3>Kredit ' . $add_kredit . '</h3>';
                    echo '<div>';
                    echo total_keuangan($post_id, 'kredit', true);
                    echo '<div>';


                    echo '<div class="laba-rugi-total">';
                    echo '<table class="widefat fixed" cellspacing="0">';
                    echo '<tr style="background-color:#333;color:#fff;">';
                    echo '<td style="text-align:left;color:#fff">';
                    echo 'Laba / Rugi';
                    echo '</td>';
                    echo '<td style="color:#fff;">';
                    $total = total_keuangan($post_id, 'debit', false) - total_keuangan($post_id, 'kredit', false);
                    echo 'Rp ' . number_format($total, 2, ',', '.');
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                    echo '</div>';
                    ?>
                    <div>
                    </div>
        <?php
            break;
        case 'nama_pembeli':
            echo get_post_meta($post_id, 'nama_pembeli', true);
            $nomor_whatsapp = get_post_meta($post_id, 'nomor_telephone', true);
            echo $nomor_whatsapp ? '-' . $nomor_whatsapp : '';
            break;
    }
}

//KOLOM KARYAWAN
// Add the custom columns to the book post type:
add_filter('manage_karyawan_posts_columns', 'set_custom_edit_karyawan_columns');
function set_custom_edit_karyawan_columns($columns)
{
    $columns['jabatan'] = __('Jabatan', 'your_text_domain');
    $columns['no_telephone'] = __('No. Telephon', 'your_text_domain');
    $columns['divisi'] = __('Divisi', 'your_text_domain');
    $columns['gaji'] = __('Gaji', 'your_text_domain');
    $columns['tanggal_mulai_kerja'] = __('Lama Bekerja', 'your_text_domain');

    return $columns;
}
// Add the data to the custom columns for the book post type:
add_action('manage_karyawan_posts_custom_column', 'custom_karyawan_column', 10, 2);
function custom_karyawan_column($column, $post_id)
{
    switch ($column) {
        case 'jabatan':
            echo get_post_meta($post_id, 'jabatan', true);
            break;
        case 'no_telephone':
            echo get_post_meta($post_id, 'no_telephone', true);
            break;
        case 'divisi':
            echo get_post_meta($post_id, 'divisi', true);
            break;
        case 'gaji':
            $gaji = get_post_meta($post_id, "gaji", true);
            $gaji = $gaji != '' ? number_format($gaji, 2, ',', '.') : '-';
            echo '@Rp ' . $gaji;

            break;
        case 'tanggal_mulai_kerja':
            $waktu1 = get_post_meta($post_id, 'tanggal_mulai_kerja', true);
            $waktu2 = date('Y-m-d');
            $lama = $waktu1 ? get_date_diff(strtotime($waktu1), strtotime($waktu2), $precision = 2) : '-';
            echo $lama;
            break;
    }
}


//KOLOM KEUANGAN
// Add the custom columns to the book post type:
add_filter('manage_keuangan_posts_columns', 'set_custom_edit_keuangan_columns');
function set_custom_edit_keuangan_columns($columns)
{
    unset($columns['author']);
    $columns['nominal'] = __('Nominal', 'your_text_domain');
    $columns['project'] = __('project', 'your_text_domain');
    $columns['karyawan'] = __('Karyawan', 'your_text_domain');
    $columns['jenis'] = __('Jenis', 'your_text_domain');

    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_keuangan_posts_custom_column', 'custom_keuangan_column', 10, 2);
function custom_keuangan_column($column, $post_id)
{
    switch ($column) {
        case 'nominal':
            $gaji = get_post_meta($post_id, "nominal", true);
            $gaji = $gaji != '' ? number_format($gaji, 2, ',', '.') : '-';
            echo 'Rp ' . $gaji;
            break;
        case 'project':
            $project_id = get_post_meta($post_id, 'project', true);
            $project_nama = get_the_title($project_id);
            echo $project_nama;
            break;
        case 'karyawan':
            $karyawan_id = get_post_meta($post_id, 'karyawan', true);
            $karyawan_nama = get_the_title($karyawan_id);
            echo $karyawan_nama;
            break;
        case 'jenis':
            echo get_post_meta($post_id, 'jenis', true);
            break;
    }
}
