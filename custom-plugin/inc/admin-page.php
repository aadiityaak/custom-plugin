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

add_action('admin_menu', 'karyawans_register_ref_page');

/**
 * Adds a submenu page under a custom post type parent.
 */
function karyawans_register_ref_page()
{
    add_submenu_page(
        'edit.php?post_type=karyawan',
        __('Absensi Karyawan', 'textdomain'),
        __('Absensi Karyawan', 'textdomain'),
        'manage_options',
        'absensi',
        'absensi_funct'
    );
}

/**
 * Display callback for the submenu page.
 */
function absensi_funct()
{
    add_thickbox();
    global $post;
    $i = 1;
    $datas = ['a', 'b', 'c'];
    $begin = isset($_GET['date']) ? $_GET['date'] . '-01' : date('Y-m-01');
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m');
    $end = date("Y-m-t", strtotime($begin));

    $begin = new DateTime($begin);
    $end = new DateTime($end);
    $end = $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval, $end);
?>
    <div class="wrap">
        <h1><?php _e('Absensi Karyawan', 'textdomain'); ?></h1>
        <form action="?" method="get" style="margin-bottom:20px;">
            <input type="month" name="date" value="<?php echo $date; ?>">
            <input type="hidden" name="page" value="absensi">
            <input type="hidden" name="post_type" value="karyawan">
            <button class="button" type="submit">Filter</button>
        </form>
        <div style="overflow-x:auto;width:100%;">
            <div style="width: 2700px;">
                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th id="columnname" class="manage-column column-columnname" scope="col">Nama</th>
                            <?php
                            foreach ($daterange as $date) {
                                $weekpembagian = (weekOfMonth(strtotime($date->format("Y-m-d"))) % 2 == 1) ? 'eee' : 'ffffff';
                                echo '<td style="background-color:#' . $weekpembagian . '" class="column-columnname">' . $date->format("j") . ' ' . hariIndo($date->format("l"));
                                echo '</td>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th id="columnname" class="manage-column column-columnname" scope="col">Nama</th>
                            <?php
                            foreach ($daterange as $date) {
                                $weekpembagian = (weekOfMonth(strtotime($date->format("Y-m-d"))) % 2 == 1) ? 'dddddd' : 'ffffff';
                                echo '<td style="background-color:#' . $weekpembagian . '" class="column-columnname">' . $date->format("j") . '</td>';
                            }
                            ?>
                        </tr>
                    </tfoot>

                    <tbody>
                        <?php

                        $query = new WP_Query(array('post_type' => 'karyawan', 'posts_per_page' => -1));
                        if ($query->have_posts()) : ?>
                            <?php while ($query->have_posts()) : $query->the_post(); ?>
                                <tr class="<?php echo $alternate; ?>">
                                    <td class="column-columnname" style="width:150px;display:flex;">
                                        <b><?php echo get_the_title(); ?></b>
                                    </td>
                                    <?php
                                    foreach ($daterange as $date) {
                                        $weekpembagian = (weekOfMonth(strtotime($date->format("Y-m-d"))) % 2 == 1) ? 'dddddd' : 'ffffff';
                                    ?>
                                        <td style="background-color:#<?php echo $weekpembagian; ?>">
                                            <a href="#TB_inline?width=600&height=400&inlineId=modal-<?php echo $post->ID . $date->format("jmy"); ?>" class="thickbox">
                                                <span class="input-<?php echo $post->ID . $date->format("jmy"); ?>">
                                                    <?php
                                                    $statusabsen = get_post_meta($post->ID, $date->format("jmy"), true);
                                                    if ($statusabsen == 'on') {
                                                    ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill" viewBox="0 0 16 16">
                                                            <circle cx="8" cy="8" r="8" />
                                                        </svg>
                                                    <?php
                                                    } else if ($statusabsen == 'off') {
                                                    ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                                        </svg>
                                                    <?php
                                                    } else if ($statusabsen == 'lembur') {
                                                    ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-circle-fill" viewBox="0 0 16 16">
                                                            <circle cx="8" cy="8" r="8" />
                                                        </svg>
                                                    <?php
                                                    } else if ($statusabsen == 'setengah') {
                                                    ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-half" viewBox="0 0 16 16">
                                                            <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z" />
                                                        </svg>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                        </svg>
                                                    <?php
                                                    }
                                                    ?>
                                                </span>
                                            </a>
                                        </td>
                                        <div id="modal-<?php echo $post->ID . $date->format("jmy"); ?>" style="display:none;hight:50px;">
                                            <h3>
                                                Ganti Status Absensi <?php echo get_the_title(); ?>
                                                (<?php echo $date->format("d F Y"); ?>)
                                            </h3>
                                            <div>
                                                <a data-post-id="<?php echo $post->ID; ?>" data-post-meta="<?php echo $date->format("jmy"); ?>" data-post-value="on" class="update-post-meta button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-fill" viewBox="0 0 16 16">
                                                        <circle cx="8" cy="8" r="8" />
                                                    </svg>
                                                    On
                                                </a>
                                                <a data-post-id="<?php echo $post->ID; ?>" data-post-meta="<?php echo $date->format("jmy"); ?>" data-post-value="off" class="update-post-meta button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                                    </svg>
                                                    Off
                                                </a>
                                                <a data-post-id="<?php echo $post->ID; ?>" data-post-meta="<?php echo $date->format("jmy"); ?>" data-post-value="lembur" class="update-post-meta button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" class="bi bi-circle-fill" viewBox="0 0 16 16">
                                                        <circle cx="8" cy="8" r="8" />
                                                    </svg>
                                                    Lembur
                                                </a>
                                                <a data-post-id="<?php echo $post->ID; ?>" data-post-meta="<?php echo $date->format("jmy"); ?>" data-post-value="setengah" class="update-post-meta button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle-half" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z" />
                                                    </svg> 1/2
                                                </a>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif;
                        wp_reset_query(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
}


/**
 * Register a custom menu page.
 */
function wpdocs_register_my_custom_menu_page()
{
    add_menu_page(
        __('Data Perusahaan', 'textdomain'),
        'Data Perusahaan',
        'manage_options',
        'perusahaan',
        'perusahaan_funct',
        CUSTOM_PLUGIN_URL . 'assets/img/speedometer.png',
        6
    );
}
add_action('admin_menu', 'wpdocs_register_my_custom_menu_page');

function perusahaan_funct()
{
    $argskaryawan = array(
        'post_type' => 'karyawan'
    );
    $karyawan_query = new WP_Query($argskaryawan);
    $argsproject = array(
        'post_type' => 'project'
    );
    $project_query = new WP_Query($argsproject);

?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h4>Project berjalan</h4>
                    <span><?php echo $project_query->found_posts; ?> Project</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h4>Karyawan Aktif</h4>
                    <span><?php echo $karyawan_query->found_posts; ?> Orang</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h4>Omset</h4>
                    <span><?php echo $karyawan_query->found_posts; ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card w-100">
                    <h4>Gaji Lapangan</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h4>Omset</h4>
                    <span><?php echo $karyawan_query->found_posts; ?></span>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<?php
}
