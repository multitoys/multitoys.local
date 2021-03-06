<?php
	$Register = &Register::getInstance();
	$smarty = &$Register->get(VAR_SMARTY);
	/* @var $smarty Smarty */
	
	//handler for new customers by links
	if(isset($_GET['refid'])){
		
		$_RefererLogin = regGetLoginById(intval($_GET['refid']));
		if($_RefererLogin){
			
			session_register('s_RefererLogin');
			$_SESSION['s_RefererLogin'] = $_RefererLogin;
			$_SESSION['refid'] = intval($_GET['refid']);
		}
	}
	
	// <head> variables definition: title, meta

	// TITLE & META Keywords & META Description
	//set $categoryID
	if (isset($_GET["categoryID"]) || isset($_POST["categoryID"])){
		
		$categoryID = isset($_GET["categoryID"]) ? (int)$_GET["categoryID"] : (int)$_POST["categoryID"];
	}
	//set $productID
	if (!isset($_GET["productID"])){
		
		if (isset($_POST["productID"])) $productID = (int) $_POST["productID"];
	}
	else $productID = (int) $_GET["productID"];

	if (isset($categoryID) && !isset($productID) && $categoryID>0) //category page
	{
		$q = db_query("SELECT ".LanguagesManager::sql_prepareField('name').', '.LanguagesManager::sql_prepareField('meta_title')." AS name FROM ".CATEGORIES_TABLE." WHERE categoryID<>0 and categoryID<>1 and categoryID='$categoryID'") or die (db_error());
		$r = db_fetch_row($q);
		if ($r)
		{
			$page_title = strlen($r[1])?$r[1]:($r[0]." ― ".CONF_SHOP_NAME);
		}
		else
		{
			$page_title = CONF_DEFAULT_TITLE." ― ".CONF_SHOP_NAME;
		}
		$page_title = str_replace( "<", "&lt;", $page_title );
		$page_title = str_replace( ">", "&gt;", $page_title );

		$meta_tags = catGetMetaTags($categoryID);

	}
	elseif (isset($productID) && $productID>0){ //product information page

		//$q = db_phquery("SELECT ".LanguagesManager::sql_prepareField('name').', '.LanguagesManager::sql_prepareField('meta_title')." FROM ?#PRODUCTS_TABLE WHERE productID=?", $productID) or die (db_error());
		$product = new Product();
		$meta_tags = '';
		if($product->loadByID($productID)){
		//$r = db_fetch_row($q);
		//if ($r){

			
			$currentDivision = &$Register->get(VAR_CURRENTDIVISION);
			/* @var $currentDivision Division */
			if($currentDivision->UnicKey == 'discuss_product'){
//				$page_title = strip_tags(strlen($r[1])?$r[1]:str_replace('%PRODUCT_NAME%', xHtmlSpecialChars($r[0]), translate('prddiscussion_title'))." ― ".CONF_DEFAULT_TITLE);
				$page_title = strip_tags($product->meta_title?$product->meta_title:str_replace('%PRODUCT_NAME%', xHtmlSpecialChars($product->name), translate('prddiscussion_title'))." ― ".CONF_SHOP_NAME);
			}else{
				//$page_title = strlen($r[1])?$r[1]:($r[0]." ― ".CONF_DEFAULT_TITLE);
				$page_title = strlen($product->meta_title)?$product->meta_title:($product->name." ― ".CONF_SHOP_NAME);
			}
			
			

			if  ( $product->meta_description != '' )
				$meta_tags .= "<meta name=\"description\" content=\"".xHtmlSpecialChars($product->meta_description)."\">\n";
			if  ( $product->meta_keywords != '' )
				$meta_tags .= "<meta name=\"keywords\" content=\"".xHtmlSpecialChars($product->meta_keywords)."\" >\n";
			
			//$meta_tags = prdGetMetaTags($productID);
			/*if(file_exists(DIR_RSS."/{$productID}.xml")){
				$smarty->assign('rss_link',URL_RSS."/{$productID}.xml");
			}*/
			$smarty->assign('rss_link',"rssfeed.php?type=product_reviews&amp;id={$productID}");
			
			unset($product);
		}else{
			error404page();
			$page_title = CONF_DEFAULT_TITLE;
		}

		
		
	}else{ // other page
		$currentDivision = &$Register->get(VAR_CURRENTDIVISION);
		switch($currentDivision->UnicKey){
			case 'auth':
				$page_title = translate('pgn_login');
				break;
			case 'search':
				$searchstring = '';
				if(isset($_GET['searchstring']))$searchstring = $_GET['searchstring'];
				if(isset($_POST['searchstring']))$searchstring = $_POST['searchstring'];
				$page_title = translate('cpt_lbl_product_search').($searchstring?' ― ':'').$searchstring;
				break;
			case 'product_comparison':
			case 'product_comparsion':
				$page_title = translate('btn_compare_products');
				break;	
				
			case 'checkout':
				$chain_links_titles = array('your_info' => 'checkout_yourinfo_header', 'shipping' => "checkout_shipping", 'billing' => "ordr_payment_type", 'confirmation' => "ordr_order_confirmation");
				$step = isset($_GET['step'])?$_GET['step']:'';
				if(isset($chain_links_titles[$step])){
					$translated =  translate($chain_links_titles[$step]);
					if($translated){
						$page_title = $translated;
					}
				}
				
				break;
			case 'office':
				$translated =  translate('pgn_my_account');
				if($translated){
					$page_title = $translated;
				}
				break;

			default:
				$string = 'pgn_'.$currentDivision->UnicKey;
				$translated =  translate($string);
				if($string!=$translated){
					$page_title = $translated;//.":[{$string}]";
				}else{
					$page_title = '';//."[{$string}]";
				}
				break;
		}
		
		$page_title = (strlen($page_title)?$page_title.' ― '.CONF_SHOP_NAME :CONF_DEFAULT_TITLE);
		$meta_tags = "";
		if  ( CONF_HOMEPAGE_META_DESCRIPTION != "" )
			$meta_tags .= "<meta name=\"description\" content=\"".xHtmlSpecialChars(CONF_HOMEPAGE_META_DESCRIPTION)."\">\n";
		if  ( CONF_HOMEPAGE_META_KEYWORDS != "" )
			$meta_tags .= "<meta name=\"keywords\" content=\"".xHtmlSpecialChars(CONF_HOMEPAGE_META_KEYWORDS)."\" >\n";
	}

	$smarty->assign("page_title",	$page_title );
	$smarty->assign("page_meta_tags", $meta_tags );
?>