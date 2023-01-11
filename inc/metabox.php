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

// METABOX CUSTOM PACKAGE
add_filter('rwmb_meta_boxes', 'your_prefix_register_meta_boxes');

function your_prefix_register_meta_boxes($meta_boxes)
{
    $prefix = '';

    $meta_boxes[] = [
        'title'      => esc_html__('Data project', 'online-generator'),
        'id'         => 'data_project',
        'post_types' => ['project'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type'       => 'post',
                'name'       => esc_html__('Penanggung Jawab', 'online-generator'),
                'id'         => $prefix . 'penanggung_jawab',
                'post_type'  => 'karyawan',
                'field_type' => 'select',
            ],
            [
                'type'       => 'post',
                'name'       => esc_html__('Tim Produksi', 'online-generator'),
                'id'         => $prefix . 'tim_produksi',
                'post_type'  => 'karyawan',
                'field_type' => 'checkbox_list',
            ],
            [
                'type'       => 'post',
                'name'       => esc_html__('Tim Pemasangan', 'online-generator'),
                'id'         => $prefix . 'tim_pemasangan',
                'post_type'  => 'karyawan',
                'field_type' => 'checkbox_list',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Alamat', 'online-generator'),
                'id'   => $prefix . 'alamat',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Nilai Kontrak', 'online-generator'),
                'id'   => $prefix . 'nilai_kontrak',
            ],
            [
                'type' => 'image_advanced',
                'name' => esc_html__('Gallery', 'online-generator'),
                'id'   => $prefix . 'gallery',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Peta / Koordinat', 'online-generator'),
                'id'   => $prefix . 'peta',
            ],
        ],
    ];
    $meta_boxes[] = [
        'title'      => esc_html__('Data Pembeli', 'online-generator'),
        'id'         => 'data_pembeli',
        'post_types' => ['project'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type' => 'text',
                'name' => esc_html__('Nama Pembeli', 'online-generator'),
                'id'   => $prefix . 'nama_pembeli',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('No. Telephone', 'online-generator'),
                'id'   => $prefix . 'nomor_telephone',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Alamat', 'online-generator'),
                'id'   => $prefix . 'alamat',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Nama Perusahaan', 'online-generator'),
                'id'   => $prefix . 'nama_perusahaan',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Alamat Perusahaan', 'online-generator'),
                'id'   => $prefix . 'alamat_perusahaan',
            ],
        ],
    ];

    //KARYAWAN
    $meta_boxes[] = [
        'title'      => esc_html__('Data Karyawan', 'online-generator'),
        'id'         => 'data_karyawan',
        'post_types' => ['karyawan'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type' => 'text',
                'name' => esc_html__('Jabatan', 'online-generator'),
                'id'   => $prefix . 'jabatan',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Alamat', 'online-generator'),
                'id'   => $prefix . 'alamat',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('No. Telephone', 'online-generator'),
                'id'   => $prefix . 'no_telephone',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Gaji', 'online-generator'),
                'id'   => $prefix . 'gaji',
            ],
            [
                'type' => 'date',
                'name' => esc_html__('Tanggal Mulai Kerja', 'online-generator'),
                'id'   => $prefix . 'tanggal_mulai_kerja',
            ],
            [
                'type' => 'select',
                'name' => esc_html__('Divisi', 'online-generator'),
                'id'   => $prefix . 'divisi',
                'options' => [
                    'kantor' => esc_html__('Kantor', 'online-generator'),
                    'produksi'  => esc_html__('Produksi', 'online-generator'),
                    'Pemasangan'  => esc_html__('Pemasangan', 'online-generator'),
                ],
                'placeholder' => esc_html__('Pilih Divisi', 'online-generator'),
            ],
        ],
    ];
    //KEUANGAN
    $meta_boxes[] = [
        'title'      => esc_html__('Keuangan', 'online-generator'),
        'id'         => 'keuangan',
        'post_types' => ['keuangan'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type' => 'number',
                'name' => esc_html__('Nominal', 'online-generator'),
                'id'   => $prefix . 'nominal',
                'std'  => isset($_GET['nominal']) ? $_GET['nominal'] : '',
            ],
            [
                'type' => 'post',
                'name' => esc_html__('project', 'online-generator'),
                'id'   => $prefix . 'project',
                'post_type'  => 'project',
                'field_type' => 'select',
                'std'  => isset($_GET['project_id']) ? $_GET['project_id'] : '',
            ],
            [
                'type' => 'post',
                'name' => esc_html__('Penerima / Penanggung jawab', 'online-generator'),
                'id'   => $prefix . 'karyawan',
                'post_type'  => 'karyawan',
                'field_type' => 'select',
            ],
            [
                'type' => 'select',
                'name' => esc_html__('Jenis', 'online-generator'),
                'id'   => $prefix . 'jenis',
                'options' => [
                    'debit' => esc_html__('Debit', 'online-generator'),
                    'kredit'  => esc_html__('Kredit', 'online-generator'),
                ],
                'std'  => isset($_GET['jenis']) ? $_GET['jenis'] : '',
                'placeholder' => esc_html__('Jenis Transaksi', 'online-generator'),
            ],
        ],
    ];

    //invoice
    $meta_boxes[] = [
        'title'      => esc_html__('Data Penerima', 'online-generator'),
        'id'         => 'invoice',
        'post_types' => ['invoice'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type' => 'text',
                'name' => esc_html__('Nama', 'online-generator'),
                'id'   => $prefix . 'nama',
                'std'  => isset($_GET['nama']) ? $_GET['nama'] : '',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Jabatan', 'online-generator'),
                'id'   => $prefix . 'jabatan',
                'std'  => isset($_GET['jabatan']) ? $_GET['jabatan'] : '',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Nama Perusahaan', 'online-generator'),
                'id'   => $prefix . 'nama_perusahaan',
                'std'  => isset($_GET['nama_perusahaan']) ? $_GET['nama_perusahaan'] : '',
            ],
            [
                'type' => 'text',
                'name' => esc_html__('Alamat', 'online-generator'),
                'id'   => $prefix . 'alamat',
                'std'  => isset($_GET['alamat']) ? $_GET['alamat'] : '',
            ],
        ],
    ];
    $meta_boxes[] = [
        'title'      => esc_html__('Data Invoice', 'online-generator'),
        'id'         => 'data_invoice',
        'post_types' => ['invoice'],
        'context'    => 'normal',
        'fields'     => [
            [
                'type' => 'date',
                'name' => esc_html__('Tanggal', 'online-generator'),
                'id'   => $prefix . 'tanggal',
                'std'  => isset($_GET['tanggal']) ? $_GET['tanggal'] : '',
            ],
          	[
                'type' => 'text',
                'name' => esc_html__('Nilai Kontrak', 'online-generator'),
                'id'   => $prefix . 'nilai_kontrak',
                'std'  => isset($_GET['nilai_kontrak']) ? $_GET['nilai_kontrak'] : '',
            ],
            [
                'type' => 'select',
                'name' => esc_html__('Status Pembayaran', 'online-generator'),
                'id'   => $prefix . 'status',
                'options' => [
                    'lunas' => esc_html__('Lunas', 'online-generator'),
                    'baru'  => esc_html__('Menunggu Pembayaran', 'online-generator'),
                ],
                'std'  => isset($_GET['jenis']) ? $_GET['jenis'] : '',
                'placeholder' => esc_html__('Jenis Transaksi', 'online-generator'),
            ],
        ],
    ];

    return $meta_boxes;
}

add_action('cmb2_admin_init', 'invoice_metabox');
/**
 * Hook in and add a metabox to demonstrate repeatable grouped fields
 */
function invoice_metabox()
{

    /**
     * Repeatable Field Groups
     */
    $cmb_group = new_cmb2_box(array(
        'id'           => 'data_produk',
        'title'        => esc_html__('Data Produk', 'cmb2'),
        'object_types' => array('invoice'),
    ));

    // $group_field_id is the field id string, so in this case: 'data_product'
    $group_field_id = $cmb_group->add_field(array(
        'id'          => 'data_product',
        'type'        => 'group',
        'description' => esc_html__('Products', 'cmb2'),
        'options'     => array(
            'group_title'    => esc_html__('Product {#}', 'cmb2'), // {#} gets replaced by row number
            'add_button'     => esc_html__('Tambah Product', 'cmb2'),
            'remove_button'  => esc_html__('Hapus Product', 'cmb2'),
            'sortable'       => true,
            // 'closed'      => true, // true to have the groups closed by default
            // 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
        ),
    ));

    /**
     * Group fields works the same, except ids only need
     * to be unique to the group. Prefix is not needed.
     *
     * The parent field's id needs to be passed as the first argument.
     */

    $cmb_group->add_group_field($group_field_id, array(
        'name'        => esc_html__('Deskripsi', 'cmb2'),
        'description' => esc_html__('Write a short description for this entry', 'cmb2'),
        'id'          => 'description',
        'type'        => 'textarea_small',
    ));

    $cmb_group->add_group_field($group_field_id, array(
        'name' => esc_html__('Jumlah Barang', 'cmb2'),
        'id'   => 'jumlah',
        'type' => 'text',
    ));

    $cmb_group->add_group_field($group_field_id, array(
        'name' => esc_html__('Harga Barang', 'cmb2'),
        'id'   => 'harga',
        'type' => 'text',
    ));
}
