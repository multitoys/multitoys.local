<?php

function smarty_modifier_is_hitu_product($productID)
{
  $query = "
SELECT count(*)  FROM SC_product_list_item
WHERE list_id = 'hitu' AND productID = '".$productID."' ";

  $res   = mysql_query($query) or die(mysql_error()."<br>$query");
  $row = mysql_fetch_row($res);
  return intval($row[0]);
}

/* vim: set expandtab: */

?>