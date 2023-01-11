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

function post_meta_images_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'size' => 'thumbnail',
            'class' => ''
        ),
        $atts,
        'post_meta_images'
    );

    $images = get_post_meta(get_the_ID(), 'gallery', false);
    $html = '';
    if (!empty($images)) {
        $html .= '<div id="gallery">';
        foreach ($images as $image) {
            $image_url = wp_get_attachment_image_src($image, $atts['size'])[0];
            $html .= '<img src="' . $image_url . '" class="' . $atts['class'] . '">';
        }
        $html .= '</div>';
    }
    return $html;
}
add_shortcode('post_meta_images', 'post_meta_images_shortcode');

function map_shortcode($atts)
{
    ob_start();
    $koordinat = get_post_meta(get_the_ID(), 'peta', true);
?>
<iframe width="100%" height="500px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
    src="https://maps.google.com/maps?q=<?php echo $koordinat; ?>&hl=es&z=14&amp;output=embed">
</iframe>
<?php
    return ob_get_clean();
}
add_shortcode('map', 'map_shortcode');

// membut shortcode gallery penggunaan [galery]
function func_gallery($attr)
{
    ob_start();
    global $post;
    $post_id = $post->ID;
    $gallery = get_post_meta($post_id, 'gallery', false);
    echo '<div class="main-carousel">';

    foreach ($gallery as $gal) {
        echo '<div class="carousel-cell text-center">';
        echo '<a href="' . wp_get_attachment_url($gal) . '" class="glightbox " data-gallery="gallery1">';
        echo '<div class="bg-slider sweet-thumbnail"  data-src="' . wp_get_attachment_url($gal) . '">';
        echo '<img src="' . wp_get_attachment_url($gal) . '" class="d-none" alt=""/>';
        echo '</div>';
        // print_r($gal);
        //    print_r(wp_get_attachment_url( $gal));
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('gallery-mobil', 'func_gallery');

function gallery_package()
{
    ob_start();
    global $post;
    $post_id = $post->ID;
    $gallery = get_post_meta($post_id, 'gallery', false);
?>

<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="true">
    <div class="carousel-indicators">
        <?php
            $i = 0;
            foreach ($gallery as $gal) {
                $n = $i++;
                $class = $n == 0 ? 'active' : '';
                echo '<div class="carousel-cell text-center">';
                echo '<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="' . $n . '" class="' . $class . '" aria-current="true" aria-label="Slide 1"></button>';
                echo '</div>';
            }
            ?>
    </div>
    <div class="carousel-inner">
        <?php
            $i = 0;

            foreach ($gallery as $gal) {
                $n = $i++;
                $class = $n == 0 ? 'active' : '';
                echo '<div class="carousel-item ' . $class . ' sweet-thumbnail-container">';
                echo '<div class="ratio-9-16 sweet-thumbnail slider-package"  data-src="' . wp_get_attachment_url($gal) . '" style="background-image: url(' . wp_get_attachment_url($gal) . ')"></div>';
                echo '</div>';
            }
            ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
        data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
        data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<?php
    return ob_get_clean();
}
add_shortcode('slider', 'gallery_package');

// membut shortcode gallery penggunaan [galery]
function funct_data_product($attr)
{
    ob_start();
    global $post;
    $post_id = $post->ID;
    $datas = get_post_meta($post_id, 'data_product', true);
    $s = 1;
?>
<div class="table-responsive">
<table class="table">
    <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Produk</th>
            <th scope="col">Harga</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $total = 0;
            foreach ($datas as $data) {
                // print_r($data);
                $total += $data['harga'] * $data['jumlah'];
            ?>
        <tr>
            <th scope="row"><?php echo $s++; ?></th>
            <td><?php echo $data['description']; ?></td>
            <td>Rp<?php echo number_format($data['harga'], 2, ',', '.'); ?></td>
            <td><?php echo $data['jumlah']; ?></td>
            <td>Rp<?php echo number_format(($data['harga'] * $data['jumlah']), 2, ',', '.'); ?></td>
        </tr>
        <?php } ?>
    </tbody>
    <thead class="thead-dark">
        <tr>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col">Total</th>
            <th scope="col">Rp<?php echo number_format($total, 2, ',', '.'); ?></th>
        </tr>
    </thead>
</table>
</div>
<?php
    return ob_get_clean();
}
add_shortcode('list-product', 'funct_data_product');