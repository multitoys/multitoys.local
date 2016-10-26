<?php
    /**
     * Created by PhpStorm.
     * User: multi
     * Date: 05.10.2016
     * Time: 13:02
     */

    define('NUM_PRODUCTS', 1959);
    define('NUM_FOR_SALE', 170);

    $end_D = 299;
    $end_C = 906 + $end_D;
    $end_B = 468 + $end_C;

    $num_A = (int)(0.2 * NUM_FOR_SALE);
    $num_B = (int)(0.2 * NUM_FOR_SALE);
    $num_C = (int)(0.2 * NUM_FOR_SALE);
    $num_D = (int)(0.4 * NUM_FOR_SALE);

    $rand_D = array();
    $rand_C = array();
    $rand_B = array();

    echo '<table><tbody><tr><td>';

    //категория D
    echo '<table><tbody><tr><td>D:</td></tr>';

    for ($i = 0; $i < $num_D; $i++) {

        $rand = mt_rand(1, $end_D);
        $rand_D[] = $rand;
    }
    $unique_D = array_unique($rand_D, SORT_NUMERIC);
    sort($unique_D, SORT_NUMERIC);

    foreach ($unique_D as $each) {
        echo '<tr><td>'.$each.'</td></tr>';
    }
    echo '</tbody></table></td>';

    //категория C
    echo '<td><table><tbody><tr><td>C:</td></tr>';

    for ($i = 0; $i < $num_C; $i++) {

        $rand = mt_rand($end_D + 1, $end_C);
        $rand_C[] = $rand;
    }

    $unique_C = array_unique($rand_C, SORT_NUMERIC);
    sort($unique_C, SORT_NUMERIC);

    foreach ($unique_C as $each) {
        echo '<tr><td>'.$each.'</td></tr>';
    }
    echo '</tbody></table></td>';

    //категория B
    echo '<td><table><tbody><tr><td>B:</td></tr>';

    for ($i = 0; $i < $num_B; $i++) {

        $rand = mt_rand($end_C + 1, $end_B);
        $rand_B[] = $rand;
    }

    $unique_B = array_unique($rand_B, SORT_NUMERIC);
    sort($unique_B, SORT_NUMERIC);

    foreach ($unique_B as $each) {
        echo '<tr><td>'.$each.'</td></tr>';
    }
    echo '</tbody></table></td>';

    //категория A
    echo '<td><table><tbody><tr><td>A:</td></tr>';

    for ($i = 0; $i < $num_A; $i++) {

        $rand = mt_rand($end_B + 1, NUM_PRODUCTS);
        $rand_A[] = $rand;
    }

    $unique_A = array_unique($rand_A, SORT_NUMERIC);
    sort($unique_A, SORT_NUMERIC);

    foreach ($unique_A as $each) {
        echo '<tr><td>'.$each.'</td></tr>';
    }

    echo '</tbody></table></td></tr></tbody></table>';