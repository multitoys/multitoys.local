<?php
    /**
     * Created by PhpStorm.
     * User: multi
     * Date: 05.10.2016
     * Time: 13:02
     */

//    $WshShell = new COM("WScript.Shell");
//    $class_vars = get_class_vars(get_class($WshShell));
//
//    foreach ($class_vars as $name => $value) {
//        echo "$name : $value\n";
//    }

    //    var_dump($WshShell);
//    $oExec = $WshShell->Run("perfmon.exe", 7, false);
//    define('xlEdgeBottom', 9);
//    define('xlContinuous', 1);
//    define('xlThin', 2);
//    define('xlAutomatic', -4105);
//
//    $ex = new COM("Excel.Application", NULL, CP_UTF8) or Die ("Did not instantiate Excel");
//    $wb = $ex->Application->Workbooks->Add();
//    $ws = $wb->Worksheets(1);
//
//    $xra = $ws->Range("A1:A6");
//
//    $bs = $xra->Borders(xlEdgeBottom);
//    $bs->LineStyle = xlContinuous;
//    $bs->Weight = xlThin;
//    $bs->ColorIndex = xlAutomatic;
//
//    function xls2pdf($doc,$pdf)
//    {
//
//// Pas de paramétres requis
//        $empty = new VARIANT();
//
//        /* Supression du pdf si il existe */
//        if(file_exists ($pdf))
//            @unlink($pdf);
//
//        /* Démarrage de Word */
//        $w = new COM("excel.application") or die("Impossible d'instancier l'application Word");
//
//        /* Test de Word 2007 */
//        if($w->Version > 11)
//        {
//            /* Amener Word devant */
//            $w->Visible = 1;
//
//            /* Test du fichier */
//            if(file_exists ($doc))
//                $w->Workbooks->Open($doc);
//            else
//                return false;
//
//            /* Quelques commandes */
//            $w->Workbooks[1]->ExportAsFixedFormat(0,$pdf);
//
//            /* Fermeture de word */
//            $w->Workbooks[1]->Close(false);
//        }
//        $w->Quit();
//
//        /* Libération des ressources */
//        $w = null;
//        unset($w);
//
//        /* Test du fichier */
//        if(file_exists ($pdf))
//            return true;
//        else
//            return false;
//
//    }
//
//    if(xls2pdf('c:\users\multi\desktop\2016.xls','c:\users\multi\desktop\test.pdf'))
//        echo "ok";
//    else
//        echo "nok";
//    $val = 5;
//    srand($val);

//    $num_products = 4359;

    define('NUM_PRODUCTS', 2933);
    define('NUM_AKCIA', 170);

    $end_D = 696;
    $end_C = 771 + $end_D;
    $end_B = 880 + $end_C;

    $num_A = (int)(0.2 * NUM_AKCIA);
    $num_B = (int)(0.3 * NUM_AKCIA);
    $num_C = (int)(0.1 * NUM_AKCIA);
    $num_D = (int)(0.4 * NUM_AKCIA);

//    $sum = $num_A + $num_B + $num_C + $num_D;
//    echo $sum.'<br>';

    $rand_D = array();
    $rand_C = array();
    $rand_B = array();

    echo '<table><tbody><tr><td>';

    //категория D
    echo '<table><tbody><tr><td>D:</td></tr>';

    for ($i = 0; $i < $num_D; $i++) {

        $rand = rand(1, $end_D);
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

        $rand = rand($end_D + 1, $end_C);
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

        $rand = rand($end_C + 1, $end_B);
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

        $rand = rand($end_B + 1, NUM_PRODUCTS);
        $rand_A[] = $rand;
    }

    $unique_A = array_unique($rand_A, SORT_NUMERIC);
    sort($unique_A, SORT_NUMERIC);

    foreach ($unique_A as $each) {
        echo '<tr><td>'.$each.'</td></tr>';
    }

    echo '</tbody></table></td></tr></tbody></table>';