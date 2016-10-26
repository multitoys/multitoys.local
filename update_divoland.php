<?php
    
    $start = microtime(true);
    ini_set('display_errors', true);
    define('DIR_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/published/SC/html/scripts');
    
    include_once(DIR_ROOT . '/includes/init.php');
    include_once(DIR_CFG . '/connect.inc.wa.php');
    include(DIR_FUNC . '/setting_functions.php');
    include(DIR_FUNC . '/import_functions.php');
    
    $a = false;
    
    if (isset($_SESSION) &&
        array_key_exists('log', $_SESSION) &&
        $_SESSION['log'] === 'sales'
    ) {
        $a = true;
    }
    
    if ($a) {
        
        echo(<<<TAG

			<html>
				<head>
					<link rel='stylesheet' type='text/css' href='css/import.css'>
				</head>
				<body>

TAG
        );
        
        $DB_tree = new DataBase();
        $DB_tree->connect(SystemSettings::get('DB_HOST'), SystemSettings::get('DB_USER'), SystemSettings::get('DB_PASS'));
        $DB_tree->selectDB(SystemSettings::get('DB_NAME'));
        $TABLE_CONC = 'Conc__divoland';

//        $values = 'currency_value, currency_value_old';
        $values = 'currency_value';
        $usd = getValues($values, 'Conc__competitors', 'CCID = 1');
        $archive_dir = $_SERVER['DOCUMENT_ROOT'] . '/upload/';
        
        //----------- Импорт товаров -----------
        $filename = $archive_dir . 'divoland_mt.csv';
        $file = file($filename);
        $rowcount = count($file);
        
        echo('<h1>Импорт товаров ...(' . $rowcount . ')</h1><hr><br>');
        echo(<<<'TAG'

            <div id='products' >
                <div style='width:0px;'></div>
            </div>

TAG
        );
        
        if (!$rowcount) {
            die(showError("CSV-файл ($filename) не содержит данных! (rowcount = $rowcount)"));
        }
        $no = 0;
        $row = 0;
        $percent = 0;
        $replace_name = array('&laquo;', '&raquo;', '&rdquo;', '&ldquo;', '&quot;', '&#039;', '\'', '.', '"', '„');
        
        if (($handle = fopen($filename, 'r')) !== false) {
            
            updateValue($TABLE_CONC, 'enabled = 0');
            
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                set_time_limit(0);
                if ($row === 0) {
                    for ($i = 0; $i <= 10; $i++) {
                        $column = decodeCodepage1251($data[$i]);
                        
                        switch ($column) {
                            case 'Родительская категория':
                                $pa = $i;
                                break;
                            case   'Категория':
                                $ca = $i;
                                break;
                            case   'ID поставщика':
                                $co = $i;
                                break;
                            case 'Наименование':
                                $na = $i;
                                break;
                            case 'Цена':
                                $pr = $i;
                                break;
                        }
                    }
                    $row++;
                    continue;
                }
                $parent = mysql_real_escape_string(decodeCodepage1251($data[$pa]));
                $category = mysql_real_escape_string(decodeCodepage1251($data[$ca]));
                if (!$parent) {
                    $parent = $category;
                }
                $code = mysql_real_escape_string(decodeCodepage1251($data[$co]));
                $name = mysql_real_escape_string(trim(str_replace($replace_name, '', decodeCodepage1251($data[$na]))));
                $price = (double)$data[$pr];
    
                if ($code) { 
                // Исправление цен
                    if (!is_numeric($price)) {
                        $price = preg_replace('/[^0-9.]/', '', $price);
                    }
                    $price_usd = $price / $usd->currency_value;
        
                    $values = 'productID, price_uah, price_usd';
                    $data_old = getValues($values, $TABLE_CONC, "code = '$code'");
                    $date_modified = date("Y-m-d");
        
                    if (!$data_old->productID) {
                        $query = "
                            INSERT INTO $TABLE_CONC
                                        (parent, category, code, name, price_uah, price_usd, date_modified)
                            VALUES      ('$parent', '$category', '$code', '$name', $price, $price_usd, '$date_modified')
                          ";
                        $res = mysql_query($query) or die(mysql_error() . "<br>$query");
                        $no++;
            
                    } else {
            
                        $date_modified = (abs(($data_old->price_uah / $price) - 1) * 100 <= 3 ||
                            abs(($data_old->price_usd / $price_usd) - 1) * 100 <= 3)
                            ? '' : ", date_modified='$date_modified'";
            
                        $query = "
                                UPDATE
                                        $TABLE_CONC
    
                                    SET     parent    = '$parent',
                                            category  = '$category',
                                            name      = '$name',
                                            price_uah = $price,
                                            price_usd = $price_usd,
                                            enabled   = 1
                                        $date_modified
    
                                WHERE  productID = $data_old->productID";
                        $res = mysql_query($query) or die(mysql_error() . "<br>$query");
                    }
                }
                $row++;
                $progress = round(($row / ($rowcount - 1) * 100), 0, PHP_ROUND_HALF_DOWN);
                if ($progress > $percent) {
                    $percent = $progress . '%';
                    progressBar('products', $percent, $start2);
                    buferOut();
                }
            }
            fclose($handle);
        }
        echo(
            '<span style="color:blue;">
         <br>Обработано ' . $row . ' товаров</span><br>
         <br>Новых ' . $no . ' товаров</span><br>
        ');
        
        // Оптимизация таблиц
        //DeleteRow($TABLE_CONC, 'price_uah = 0.00');
        $query = "UPDATE $TABLE_CONC SET parent='', category='' WHERE enabled=0";
        $res = mysql_query($query) or die(mysql_error() . "<br>$query");
        
        $query = "OPTIMIZE TABLE $TABLE_CONC";
        $res = mysql_query($query) or die(mysql_error() . "<br>$query");
        mysql_close();
        
        progressBar('products', $percent, true);
        echo('
        <br>
          <div id=\'end\'>Импорт завершен!</div>
      ');
        debugging($start);
    } else {
//        var_dump($_SESSION);
        die('NO LOGIN SESSION');
    }