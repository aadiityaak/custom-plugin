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

function weekOfMonth($date)
{
    //Get the first day of the month.
    $firstOfMonth = strtotime(date("Y-m-01", $date));
    //Apply above formula.
    return weekOfYear($date) - weekOfYear($firstOfMonth) + 1;
}

function weekOfYear($date)
{
    $weekOfYear = intval(date("W", $date));
    if (date('n', $date) == "1" && $weekOfYear > 51) {
        // It's the last week of the previos year.
        return 0;
    } else if (date('n', $date) == "12" && $weekOfYear == 1) {
        // It's the first week of the next year.
        return 53;
    } else {
        // It's a "normal" week.
        return $weekOfYear;
    }
}

function total_keuangan($id_project, $jenis, $detail)
{
    ob_start();
    global $post;
    $label = ($jenis == 'debit') ? 'Pendapatan' : 'Biaya';
    $query = new WP_Query(array(
        'post_type' => 'keuangan',
        'meta_value' => $id_project,
        'meta_key'  => 'project'
    ));
    $total = 0;

    if ($detail == true) {
?>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><b>Nama</b></th>
                    <th id="columnname" class="manage-column column-columnname" scope="col"><b>Nominal</b></th>
                </tr>
            </thead>
            <tbody>
            <?php
        }
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $nominal = get_post_meta($post->ID, 'nominal', true);
                $jenis_keuangan = get_post_meta($post->ID, 'jenis', true);
                if ($jenis_keuangan == $jenis) {
                    $total += $nominal;
                    if ($detail == true) {
                        echo '<tr>';
                        echo '<td>';
                        echo get_the_title();
                        echo '</td>';
                        echo '<td>';
                        echo 'Rp ' . number_format($nominal, 2, ',', '.');
                        echo '</td>';
                        echo '</tr>';
                    }
                }
            } // end while
        } // end if
        if ($detail == true) {
            echo '<tr style="background-color:#777777;color:#fff;">';
            echo '<td style="color:#fff;">';
            echo 'Total ' . $label;
            echo '</td>';
            echo '<td style="color:#fff;">';
            echo 'Rp ' . number_format($total, 2, ',', '.');
            echo '</td>';
            echo '</tr>';
            ?>
            </tbody>
        </table>
<?php
        } else {
            echo $total;
        }
        wp_reset_query();
        return ob_get_clean();
    }

    function get_date_diff($time1, $time2, $precision = 2)
    {
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
        }

        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();

        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }
    function hariIndo($hariInggris)
    {
        switch ($hariInggris) {
            case 'Sunday':
                return 'Minggu';
            case 'Monday':
                return 'Senin';
            case 'Tuesday':
                return 'Selasa';
            case 'Wednesday':
                return 'Rabu';
            case 'Thursday':
                return 'Kamis';
            case 'Friday':
                return 'Jumat';
            case 'Saturday':
                return 'Sabtu';
            default:
                return '-';
        }
    }
