<?php

    ignore_user_abort(true);
    set_time_limit(0);

    $start = microtime(true);

    define('DIR_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/published/SC/html/scripts');

    include_once(DIR_ROOT . '/includes/init.php');
    include_once(DIR_CFG . '/connect.inc.wa.php');
    include(DIR_FUNC . '/setting_functions.php');

    $DB_tree = new DataBase();
    $DB_tree->connect(SystemSettings::get('DB_HOST'), SystemSettings::get('DB_USER'), SystemSettings::get('DB_PASS'));
    $DB_tree->selectDB(SystemSettings::get('DB_NAME'));
    define('VAR_DBHANDLER', 'DBHandler');

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    //Распаковка архива
    $archive_dir = $_SERVER['DOCUMENT_ROOT'] . '/upload/';
    $dest_dir = $_SERVER['DOCUMENT_ROOT'] . '/temp/import/';

    $zip = new ZipArchive();
    $fileName = $archive_dir . 'multi.zip';

    if ($zip->open($fileName) !== true) {
        $subject = 'Ошибка при распаковке архива multi.zip';
        $body = $subject;
        mail('multitoys.dp@gmail.com', $subject, $body, $headers);
        exit(1);
    }

    RemoveDir($dest_dir);
    $zip->extractTo($dest_dir);

    //----------- Импорт категорий -----------
    $filename = $dest_dir . 'categories.csv';
    $file = file($filename);
    $rowcount = count($file);

    if (!$rowcount) {
        $subject = "CSV-файл ($filename) не содержит данных! (rowcount = $rowcount)";
        $body = $subject;
        mail('multitoys.dp@gmail.com', $subject, $body, $headers);
        exit(1);
    }
    if (($handle = fopen($filename, 'r')) !== false) {

        // Идентификатор категории "новинки"
        define('CAT_NOVINKI_ID', GetValue('categoryID', 'SC_categories', "name_ru = 'Новинки'"));

        // Идентификатор категории "акция"
        define('CAT_AKCIA_ID', GetValue('categoryID', 'SC_categories', "name_ru = 'Акция'"));

        // Идентификатор категории "акция "Баллы"
        define('CAT_SUPER_PRICE_ID', GetValue('categoryID', 'SC_categories', "name_ru = 'Суперцена'"));
        // Идентификатор категории "Все бонусные товары"
        define('CAT_BONUS_ID', GetValue('categoryID', 'SC_categories', "name_ru = 'Все бонусные товары'"));

        $query = 'SELECT categoryID FROM SC_categories WHERE allow_products_comparison';
        $res = mysql_query($query) or exit(1);

        $ids = array();
        $ids[] = -1;

        while ($row = mysql_fetch_object($res)) {
            $ids[] = $row->categoryID;
        }
        $ids = implode(',', $ids);

        $query
            =
            "UPDATE SC_categories SET show_subcategories_products = 0 WHERE categoryID IN ($ids) AND parent IS NOT NULL AND categoryID NOT IN ("
            . CAT_NOVINKI_ID . ", " . CAT_AKCIA_ID . ", " . CAT_BONUS_ID . ", " . CAT_SUPER_PRICE_ID . ")";
        $res = mysql_query($query) or exit(1);

        $no = 0;
        $row = 0;
        $percent = 0;

        while (($data = fgetcsv($handle, 255, ';')) !== false) {
            $row++;
            $id = $data[1];
            $parent = ($parent === 'NULL') ? 1 : $parent;
            $parent = ($parent) ? 1 : $parent;
            $parent = $data[2];
            $name = mysql_real_escape_string(DecodeCodepage1251($data[3]));

            $slug = Str2Url("$name") . '-' . $id;

            if (is_numeric($id) && is_numeric($parent)) {
                $est = GetValue('categoryID', 'SC_categories', "categoryID = $id");
                if (!$est) {
                    $query
                        = "INSERT INTO SC_categories (

                                                    slug,
                                                    categoryID,
                                                    parent,
                                                    name_ru,
                                                    allow_products_comparison,
                                                    show_subcategories_products

                                                ) VALUES (

                                                    '$slug',
                                                    $id,
                                                    $parent,
                                                    '$name',
                                                    1,
                                                    1
                                                )";
                    $res = mysql_query($query) or exit(1);
                } else {
                    $query
                        = "UPDATE SC_categories SET
                                                show_subcategories_products = 1,
                                                parent = $parent,
                                                name_ru = '$name',
                                                slug = '$slug'
                                                WHERE categoryID = $est";
                    $res = mysql_query($query) or exit(1);
                }
                $no++;
            } else {
                $subject = "Неверный id ($id) или parent ($parent) в строке $row (категории)";
                $body = $subject;
                mail('multitoys.dp@gmail.com', $subject, $body, $headers);
            }
        }

        fclose($handle);

        $query
            = "DELETE FROM SC_categories WHERE show_subcategories_products = FALSE AND categoryID IN ($ids) and parent IS NOT NULL";
        $res = mysql_query($query) or exit(1);
    } else {
        $subject = "Ошибка в при открытии файла: $filename";
        $body = $subject;
        mail('multitoys.dp@gmail.com', $subject, $body, $headers);
        exit(1);
    }

    //----------- Импорт товаров -----------
    $filename = $dest_dir . 'products.csv';
    $file = file($filename);
    $rowcount = count($file);

    if (!$rowcount) {
        $subject = "CSV-файл ($filename) не содержит данных! (rowcount = $rowcount)";
        $body = $subject;
        mail('multitoys.dp@gmail.com', $subject, $body, $headers);
        exit(1);
    }
    $no = 0;
    $new_id = 0;
    $row = 0;

    if (($handle = fopen($filename, "r")) !== false) {
        $start2 = microtime(true);
        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            set_time_limit(0);

            if ($row !== 0) {

                $no++;
                $ua = $data[0];
                $id = $data[1];
                $code = mysql_real_escape_string(DecodeCodepage1251($data[2]));
                $catid = is_numeric($data[3]) ? $data[3] : 1;
                $price = $data[4];
                $bonus = $data[6];
                $name = mysql_real_escape_string(trim(DecodeCodepage1251($data[7])));
                $skidka = $data[8];
                $hit = $data[9];
                $new = $data[10];
                $new_postup = $data[11];
                $akcia = $data[12];
                $ostatok = mysql_real_escape_string(DecodeCodepage1251($data[13]));
                $doza = $data[15];
                $box = $data[18];
                $minorder = $data[21];
                $oldprice = $data[22];
                $zakaz = $data[26];
                $brand = mysql_real_escape_string(DecodeCodepage1251($data[27]));
                $purchase = $data[28];

                // Исправление цен
                if (!is_numeric($price)) {
                    $price = preg_replace('/[^0-9.]/', '', $price);
                }
                if (!is_numeric($oldprice)) {
                    $oldprice = preg_replace('/[^0-9.]/', '', $oldprice);
                }
                if (!is_numeric($purchase)) {
                    $purchase = preg_replace('/[^0-9.]/', '', $purchase);
                }

                $ua = ($ua > 1) ? 1 : 0;
                $skidka = is_numeric($skidka) ? $skidka : 0;
                $bonus = is_numeric($bonus) ? $bonus : 0;
                $hit = ($hit > 0) ? $hit : 0;
                $new = ($new > 0) ? 7 : 5;
                $new_postup = ($new_postup > 0) ? $new_postup : 0;
                $akcia = ($akcia > 0) ? 1 : 0;
                $akcia_skidka = ($akcia > 0) ? (1 - $price / $oldprice) * 100 : 0;
                $akcia_skidka = is_numeric($akcia_skidka) ? $akcia_skidka : 0;
                $akcia_bally = 0;
                if ($price > 0) {
                    $akcia_bally = (int)($bonus / $price);
                }
                $akcia_bally = ($akcia_bally > 2) ? 1 : 0;
                $doza = is_numeric($doza) ? $doza : 0;
                $box = is_numeric($box) ? $box : 0;
                $oldprice = ($oldprice > $price) ? $oldprice : 0;
                $minorder = ($minorder > 0) ? 1 : 0;
                $zakaz = ($zakaz > 0) ? 1 : 0;
                $slug = Str2Url("$name") . '-' . $id;

                if (is_numeric($id) && strlen($id) > 4) {

                    $productID = GetValue('productID', 'SC_products', "code_1c = '$id'");
                    $margin = 0;
                    if ($purchase > 0) {
                        $margin = round((100 * ($price / $purchase) - 100), 0);
                    }

                    if (!$productID) {
                        $query
                            = "
							INSERT INTO SC_products
								   (categoryID, purchase, Price, Bonus, in_stock, items_sold, list_price, akcia,   akcia_skidka,   enabled, product_code, sort_order , ordering_available, slug , name_ru, skidka , code_1c, ostatok  , ukraine  , doza  ,  box,   minorder   , zakaz, brand, eproduct_available_days)
							VALUES
									($catid    , $purchase, $price, $bonus, 200 , $hit  , $oldprice,$akcia, $akcia_skidka, 1     , '$code'     , $new_postup, 1                 , '$slug','$name',  $skidka, '$id'  ,'$ostatok',$ua,$doza,$box, $minorder, $zakaz, '$brand', $new)";
                        $res = mysql_query($query) or exit(1);
                        $productID = mysql_insert_id();
                        $new_id++;
                    } else {

                        $query
                            = "
							UPDATE SC_products
							SET categoryID = $catid,
							    purchase = $purchase,
								Price = $price,
								Bonus = $bonus,
								in_stock = 200,
								items_sold = $hit,
								enabled = 1,
                                    list_price              = $oldprice,
                                    akcia                   = $akcia,
                                    akcia_skidka            = $akcia_skidka,
								product_code = '$code',
								sort_order = $new_postup,
								ordering_available = 1,
								slug = '$slug',
								name_ru = '$name',
								skidka = $skidka ,
								ostatok = '$ostatok',
								ukraine = $ua,
                                    doza                    = $doza,
                                    box                     = $box,
                                    zakaz                   = $zakaz,
								brand= '$brand',
								eproduct_available_days = $new
							WHERE
								productID = $productID
						";
                        $res = mysql_query($query) or exit(1);

                        $query = "DELETE FROM SC_category_product WHERE productID = $productID";
                        $res = mysql_query($query) or exit(1);

                        $query = "DELETE FROM SC_product_list_item WHERE productID = $productID";
                        $res = mysql_query($query) or exit(1);
                    }
                    //--------- Дополнительные категории и Списки продуктов---------

                    if ($akcia) {
                        $query = "INSERT INTO SC_category_product 
                                  VALUES ($productID, " . CAT_AKCIA_ID . ", 1)";
                        $res = mysql_query($query) or exit(1);
                        $query = "INSERT INTO SC_product_list_item (list_id, productID, priority) 
                                  VALUES ('akcia', $productID, 1)";
                        $res = mysql_query($query) or exit(1);
                    }
                    if ($new === 7) {
                        $query = "INSERT INTO SC_product_list_item (list_id, productID, priority) 
                                  VALUES ('newitems', $productID, 1)";
                        $res = mysql_query($query) or exit(1);
                        $query = "INSERT INTO SC_category_product 
                                  VALUES ($productID, " . CAT_SUPER_PRICE_ID . ", 1)";
                        $res = mysql_query($query) or exit(1);
                    }
                    if ($bonus) {
                        $query = "INSERT INTO SC_category_product 
                                  VALUES ($productID, " . CAT_BONUS_ID . ", 1)";
                        $res = mysql_query($query) or exit(1);
                    }

                    if ($hit) {
                        $query = "INSERT INTO SC_product_list_item (list_id, productID, priority) 
                                  VALUES ('hitu', $productID, 1)";
                        $res = mysql_query($query) or exit(1);
                    }
                    if ($new_postup) {
                        $query = "INSERT INTO SC_product_list_item (list_id, productID, priority, date, ukraine) 
                                  VALUES ('newitemspostup', $productID, 1, $new_postup, $ua)";
                        $res = mysql_query($query) or exit(1);
                    }
                } else {
                    $subject = "Неверный id ($id) (строка $row) - позиция проигнорирована";
                    $body = $subject;
                    mail('multitoys.dp@gmail.com', $subject, $body, $headers);
                }
            } else {
                $usd = $data[0];
                if (!is_numeric($usd)) {
                    $usd = preg_replace('/[^0-9.]/', '', $usd);
                }
                $query = "UPDATE SC_currency_types SET currency_value = 1/$usd WHERE CID = 10";
                $res = mysql_query($query) or exit(1);
            }
            $row++;
        }
        fclose($handle);
    } else {
        $subject = "Ошибка в при открытии файла: $filename";
        $body = $subject;
        mail('multitoys.dp@gmail.com', $subject, $body, $headers);
        exit(1);
    }


    $res_end = "Обработано $no товаров.\r\n Новых $new_id товаров";

    $query = "SELECT productID FROM SC_products WHERE  in_stock =100";
    $res = mysql_query($query) or exit(1);
    
    $products_disabled = array();

    while ($enabled = mysql_fetch_row($res)) {
        $products_disabled[] = $enabled[0];
    }
    $products_disabled = implode(',', $products_disabled);

    $query = 'UPDATE SC_products SET enabled = FALSE, items_sold = 0 WHERE productID IN (' . $products_disabled . ')';
    $res = mysql_query($query) or exit(1);


    $query = 'UPDATE SC_products SET in_stock =100 WHERE in_stock = 200';
    $res = mysql_query($query) or exit(1);

    $query = 'UPDATE SC_products SET `min_order_amount` = `box` WHERE `zakaz` = 1 OR `minorder` = 1';
    $res = mysql_query($query) or exit(1);

    $query = 'DELETE FROM SC_category_product WHERE priority = 0';
    $res = mysql_query($query) or exit(1);

    $query = 'UPDATE SC_category_product SET priority = 0';
    $res = mysql_query($query) or exit(1);

    $query = 'DELETE FROM SC_product_list_item WHERE priority = 0';
    $res = mysql_query($query) or exit(1);

    $query = 'UPDATE SC_product_list_item SET priority = 0';
    $res = mysql_query($query) or exit(1);

    $query = 'UPDATE SC_import_lock SET `import` = `import` + 1';
    $res = mysql_query($query) or exit(1);

    $new_count = 500;

    $query = "SELECT productID FROM SC_products WHERE enabled = 1 AND in_stock =100 ORDER BY code_1c DESC LIMIT $new_count";
    $res = mysql_query($query) or die('Ошибка в запросе: ' . mysql_error() . '<br>' . $query);

    while ($ids = mysql_fetch_row($res)) {
        $values[] = $ids[0];
    }
    $query = "INSERT INTO SC_category_product VALUES (".implode(', 66666, 0), (', $values).", 66666, 0)";
    $res = mysql_query($query) or exit(1);
    
    // Оптимизация таблиц
    $query = 'OPTIMIZE TABLE 
                    `SC_auth_log`, `SC_categories`, `SC_category_product`, `SC_currency_types`, `SC_customers`, 
                    `SC_customer_addresses`, `SC_customer_reg_fields_values`, `SC_ordered_carts`, `SC_orders`, 
                    `SC_order_status_changelog`, `SC_products`, `SC_product_list_item`, `SC_product_pictures`, 
                    `SC_shopping_carts`, `SC_shopping_cart_items`, `SC_subscribers`, `Search_products`';
    $res = mysql_query($query) or exit(1);
    mysql_close();

    // Удаление временных файлов и информирование об успешном окончании импорта
    RemoveDir($_SERVER['DOCUMENT_ROOT'] . '/upload/');

    $subject = 'Импорт товаров завершен!';
    $body_end = $subject . $res_end . "\r\n" . DebuggingForMail($start);
    mail('multitoys.dp@gmail.com', $subject, $body_end, $headers);

//    exit(0);

    /*--------- Функции ---------*/
    function get($what, $condition, $table = '')
    {
        $query = "SELECT $what FROM $table WHERE $condition LIMIT 1";
        $result = mysql_query($query) or exit(1);
        $row = mysql_fetch_row($result);

        return $row[0];
    }

    function deleteRow($table, $condition = '')
    {
        $condition = ($condition) ? "WHERE $condition" : "";
        $query = "DELETE FROM $table $condition";
        $result = mysql_query($query) or exit(1);
    }

    function optimizeTable($table)
    {
        $query = "OPTIMIZE TABLE $table";
        $res = mysql_query($query) or exit(1);
        mysql_close();
    }

    function RemoveDir($directory)
    {
        $dir = opendir($directory);
        while (($file = readdir($dir))) {
            if (is_file($directory . '/' . $file)) {
                unlink($directory . '/' . $file);
            } else {
                if (is_dir($directory . '/' . $file) && ($file != '.') && ($file != '..')) {
                    RemoveDir($directory . '/' . $file);
                }
            }
        }
        closedir($dir);

        return true;
    }

    function GetValue($what, $table, $condition)
    {
        $query = "SELECT $what FROM $table WHERE $condition LIMIT 1";
        $result = mysql_query($query) or exit(1);
        $row = mysql_fetch_row($result);

        return $row[0];
    }

    function UpdateValue($table, $new_value, $condition = '')
    {
        $condition = ($condition) ? "WHERE $condition" : '';
        $query = "UPDATE $table SET $new_value $condition";
        $result = mysql_query($query) or exit(1);
    }

    function DecodeCodepage($text)
    {
        $s = mb_detect_encoding($text);
        $q = iconv($s, 'UTF-8', $text);

        return $q;
    }

    function DecodeCodepage1251($text)
    {
        $s = 'windows-1251';
        $q = iconv($s, 'UTF-8', $text);

        return $q;
    }

    function Rus2Translit($string)
    {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
        );

        return strtr($string, $converter);
    }

    function Str2Url($str)
    {
        $str = Rus2Translit($str);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        $str = str_replace("'", '', $str);
        $str = preg_replace('~\-{2,}~', '-', $str);
        $str = trim($str, '-');

        return $str;
    }

    function DebuggingForMail($start)
    {
        $memoscript_peak = memory_get_peak_usage(true) / 1048576;
        $time = microtime(true) - $start;
        $res = 'Скрипт выполнялся: ' . $time . ".\r\n Пик оперативной памяти: " . $memoscript_peak . "\r\n";

        return $res;
    }