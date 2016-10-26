<?php
    function smarty_function_newcategoriestree($params, &$smarty)
    {
        $disp = '<div id="newmenu"><ul>';
        
        $disp .= '<li id=new_postup_home>
                    <a id=new_home href="auxpage_new_items/all/0/0/">Новые поступления:</a>
                  </li>'.newItemsInDates()
        ;

        $disp .= '</ul><div style="clear:both"></div></div>';

        return $disp;
    }
    
    function newItemsInDates()
    {
        $disp = '';
        
        $sqlC = "SELECT DISTINCT date
                         FROM SC_product_list_item
                         WHERE list_id = 'newitemspostup'
                         ORDER BY date ASC"
        ;
        
        if ($rC = mysql_query($sqlC)) {
            
            if (mysql_num_rows($rC) > 0) {

                while ($resC = mysql_fetch_assoc($rC)) {
    
                    $date = $resC['date'];
                    $date_postup = calculateDate($date);

    
                    $sqlD = "SELECT DISTINCT ukraine
                         FROM SC_product_list_item
                         WHERE date = $date
                         ORDER BY ukraine ASC"
                    ;
                    $rD = mysql_query($sqlD);
    
                    $disp .= '<li class=new_postup>';
                    $disp .= '<div class=to_left><a href="/auxpage_new_items/all/'.$date.'/0/">'.$date_postup.':</a></div>';
    
                    while ($resD = mysql_fetch_assoc($rD)) {
                        $class = '';
                        $manufactured_url = 'ukraine';
                        $manufactured_link = 'Украина';
    
                        if ($resD['ukraine'] < 1) {
                            $class = ' class=to_left';
                            $manufactured_url = 'china';
                            $manufactured_link = 'Китай';
                        }
                        $disp .= '<div'.$class.'><a href="/auxpage_new_items/'.$manufactured_url.'/'.$date.'/0/"> <span class='.$manufactured_url.'>'.$manufactured_link.'</span></a></div>';
                    }
                    $disp .= '</li>';
    
    
                }
                $disp .= '</ul>';
            }
        }
        
        return $disp;
    }
    
    function calculateDate($date_num)
    {
        $date = time() - (($date_num - 1) * 24 * 60 * 60);
        
        return date('d-m-Y', $date);
    }