<?
session_start();


$eventManager = Bitrix\Main\EventManager::getInstance();
define('VUEJS_DEBUG', true);
// $eventManager->addEventHandler('main', 'OnEndBufferContent', function(&$content){
	
	// global $APPLICATION;
	// if($_GET['test'] == 123){
		
		
		// $page = $APPLICATION->GetCurPage(false);
		// if(preg_match('#/articles/(?:[^/]+)/(?:[^/]+)/#',$page)){
			
			
			// preg_match_all('#detail_text.*(?=(<img.+>))#',$content, $matches);
			
			// echo '<pre>';print_r($matches);echo "</pre>";
			
		// }
			

		
	// }	
	
	
// });

$eventManager->addEventHandler('catalog', 'OnGetOptimalPrice', function(
    $productId,
    $quantity = 1,
    $arUserGroups = array(),
    $renewal = "N",
    $arPrices = array(),
    $siteID = false,
    $arDiscountCoupons = false){

   $prices = \CCatalogProduct::GetByIDEx($productId);
   $regionPriceId = $_SESSION['CURRENT_LOCATION']['CURRENT']["PRICES"]["PRICE_ID"];

   if (!$regionPriceId)
      return true;

	$price=$prices['PRICES'][$regionPriceId]['PRICE'];
	$cur=$prices['PRICES'][$regionPriceId]['CURRENCY'];
	
	if ($price==0)
	{
	    $regionPriceId = 1;	
		$price=$prices['PRICES'][$regionPriceId]['PRICE'];
		$cur=$prices['PRICES'][$regionPriceId]['CURRENCY'];
	}
	
	$arDiscounts = CCatalogDiscount::GetDiscountByProduct(
        $productId,
        $arUserGroups,
        "N",
        $regionPriceId,
        $siteID
    );	

	$arDiscounts2=array();
	$arDiscounts3=array();
	foreach($arDiscounts as $key => $val){
		$arDiscounts3[$val["PRIORITY"]][]=$val;
	}
	krsort($arDiscounts3);
	$isBreak=false;
	foreach($arDiscounts3 as $key => $val){
		foreach($val as $k => $v){
			if (!$isBreak) $arDiscounts2[]=$v;
			if ($v["LAST_DISCOUNT"]=="Y") $isBreak=true;
		}
	}

	return array(
		"PRICE" => array(
			'PRICE' => $price,
			'CURRENCY' => $cur,
			'CATALOG_GROUP_ID' => $regionPriceId,
			'ELEMENT_IBLOCK_ID' => $productId,
			'VAT_INCLUDED' => 'Y'		
		),
		"DISCOUNT" => $arDiscounts2,
		"DISCOUNT_LIST" => $arDiscounts2
	);

});

 
function GetRateFromCBR($CURRENCY) 
{ 
	global $DB; 
	global $APPLICATION; 

	CModule::IncludeModule('currency');
	if(!CCurrency::GetByID($CURRENCY)) //такой валюты нет на сайте, агент в этом случае удаляется
	return false;

	$DATE_RATE=date("d.m.Y");//сегодня 
	$QUERY_STR = "date_req=".$DB->FormatDate($DATE_RATE, CLang::GetDateFormat("SHORT", $lang), "D.M.Y"); 

	//делаем запрос к www.cbr.ru с просьбой отдать курс на нынешнюю дату          
	$strQueryText = QueryGetData("www.cbr.ru", 80, "/scripts/XML_daily.asp", $QUERY_STR, $errno, $errstr); 

	//получаем XML и конвертируем в кодировку сайта          
	$charset = "windows-1251"; 
	if (preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $strQueryText, $matches)) 
	{ 
		$charset = Trim($matches[1]); 
	} 
	//$strQueryText = eregi_replace("<!DOCTYPE[^>]{1,}>", "", $strQueryText); 
	//$strQueryText = eregi_replace("<"."\?XML[^>]{1,}\?".">", "", $strQueryText); 
	$strQueryText = preg_replace("/<!DOCTYPE[^>]{1,}>/i", "", $strQueryText); 
	$strQueryText = preg_replace("/<"."\?XML[^>]{1,}\?".">/i", "", $strQueryText); 
	$strQueryText = $APPLICATION->ConvertCharset($strQueryText, $charset, SITE_CHARSET); 

	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");

	//парсим XML 
	$objXML = new CDataXML(); 
	$res = $objXML->LoadString($strQueryText); 
	if($res !== false) 
		$arData = $objXML->GetArray(); 
	else 
		$arData = false; 
	
	$NEW_RATE=Array(); 
	
	//получаем курс нужной валюты $CURRENCY 
	if (is_array($arData) && count($arData["ValCurs"]["#"]["Valute"])>0) 
	{ 
		for ($j1 = 0; $j1<count($arData["ValCurs"]["#"]["Valute"]); $j1++) 
		{ 
			if ($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["CharCode"][0]["#"]==$CURRENCY) 
			{ 
				$NEW_RATE['CURRENCY']=$CURRENCY; 
				$NEW_RATE['RATE_CNT'] = IntVal($arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Nominal"][0]["#"]); 
				//$NEW_RATE['RATE'] = DoubleVal(str_replace(",", ".", $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Value"][0]["#"]))*1.02; 
				$NEW_RATE['RATE'] = DoubleVal(str_replace(",", ".", $arData["ValCurs"]["#"]["Valute"][$j1]["#"]["Value"][0]["#"]));
				$NEW_RATE['DATE_RATE']=$DATE_RATE; 
				break; 
			} 
		} 
	} 

	if ((isset($NEW_RATE['RATE']))&&(isset($NEW_RATE['RATE_CNT']))) 
	{ 
		//курс получили, возможно, курс на нынешнюю дату уже есть на сайте, проверяем 
		CModule::IncludeModule('currency'); 
		$arFilter = array( 
			"CURRENCY" => $NEW_RATE['CURRENCY'], 
			"DATE_RATE"=>$NEW_RATE['DATE_RATE'] 
		); 
		$by = "date"; 
		$order = "desc"; 
	
		$db_rate = CCurrencyRates::GetList($by, $order, $arFilter); 
		if(!$ar_rate = $db_rate->Fetch()) //такого курса нет, создаём курс на нынешнюю дату 
			CCurrencyRates::Add($NEW_RATE); 
	} 

	//возвращаем код вызова функции, чтобы агент не "убился" 
	return 'GetRateFromCBR("'.$CURRENCY.'");'; 
}

function setActions() 
{ 
	global $DB; 
	global $APPLICATION; 

	CModule::IncludeModule("catalog");

	$dbProductDiscounts = CCatalogDiscount::GetList(
		array("SORT" => "ASC"),
		array(
				"ACTIVE" => "Y",
				"!>ACTIVE_FROM" => $DB->FormatDate(date("Y-m-d H:i:s"), 
												   "YYYY-MM-DD HH:MI:SS",
												   CSite::GetDateFormat("FULL")),
				"!<ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"), 
												 "YYYY-MM-DD HH:MI:SS", 
												 CSite::GetDateFormat("FULL")),
				"COUPON" => ""
			),
		false,
		false,
		array(
				"PRODUCT_ID"
			)
		);
	while ($arProductDiscounts = $dbProductDiscounts->Fetch())
	{
		$v=array();
		$db_props = CIBlockElement::GetProperty(14, $arProductDiscounts["PRODUCT_ID"], array("sort" => "asc"), Array("CODE"=>"HIT"));
		while($ar_props = $db_props->Fetch())
		{
			$v[]=$ar_props["VALUE"];
		}
		$vnew=$v;		
		if(($key = array_search(17, $vnew)) === false) {
			$vnew[]=17;	
		}			
		if ($vnew!=$v && $arProductDiscounts["PRODUCT_ID"]>0)
		{
			CIBlockElement::SetPropertyValuesEx($arProductDiscounts["PRODUCT_ID"], 14, array("HIT" => $vnew));
			\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(14, $arProductDiscounts["PRODUCT_ID"]);
			if (is_object($GLOBALS['CACHE_MANAGER'])) $GLOBALS['CACHE_MANAGER']->ClearByTag('iblock_id_14');
		}		
	}
	
	$dbProductDiscounts = CCatalogDiscount::GetList(
		array("SORT" => "ASC"),
		array(
				"<ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"), 
												 "YYYY-MM-DD HH:MI:SS", 
												 CSite::GetDateFormat("FULL")),
				"COUPON" => ""
			),
		false,
		false,
		array(
				"PRODUCT_ID"
			)
		);
	while ($arProductDiscounts = $dbProductDiscounts->Fetch())
	{
		$v=array();		
		$db_props = CIBlockElement::GetProperty(14, $arProductDiscounts["PRODUCT_ID"], array("sort" => "asc"), Array("CODE"=>"HIT"));
		while($ar_props = $db_props->Fetch())
		{
			$v[]=$ar_props["VALUE"];
		}
		$vnew=$v;		
		if(($key = array_search(17, $vnew)) !== false) {
			unset($vnew[$key]);
		}
	  if (count($vnew)==0) $vnew="";
		if ($vnew!=$v && $arProductDiscounts["PRODUCT_ID"]>0)
		{
			CIBlockElement::SetPropertyValuesEx($arProductDiscounts["PRODUCT_ID"], 14, array("HIT" => $vnew));
			\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(14, $arProductDiscounts["PRODUCT_ID"]);
			if (is_object($GLOBALS['CACHE_MANAGER'])) $GLOBALS['CACHE_MANAGER']->ClearByTag('iblock_id_14');
		}		
	}
	$dbProductDiscounts = CCatalogDiscount::GetList(
		array("SORT" => "ASC"),
		array(
				"ACTIVE" => "N",
				"COUPON" => ""
			),
		false,
		false,
		array(
				"PRODUCT_ID"
			)
		);
	while ($arProductDiscounts = $dbProductDiscounts->Fetch())
	{
		$v=array();		
		$db_props = CIBlockElement::GetProperty(14, $arProductDiscounts["PRODUCT_ID"], array("sort" => "asc"), Array("CODE"=>"HIT"));
		while($ar_props = $db_props->Fetch())
		{
			$v[]=$ar_props["VALUE"];
		}
		$vnew=$v;
		if(($key = array_search(17, $vnew)) !== false) {
			unset($vnew[$key]);
		}
		if (count($vnew)==0) $vnew="";
		if ($vnew!=$v && $arProductDiscounts["PRODUCT_ID"]>0)
		{
			CIBlockElement::SetPropertyValuesEx($arProductDiscounts["PRODUCT_ID"], 14, array("HIT" => $vnew));
			\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(14, $arProductDiscounts["PRODUCT_ID"]);
			if (is_object($GLOBALS['CACHE_MANAGER'])) $GLOBALS['CACHE_MANAGER']->ClearByTag('iblock_id_14');
		}
	}	

	return 'setActions();'; 
}

function setPrices() 
{ 
	global $DB; 
	global $APPLICATION; 

	CModule::IncludeModule("iblock");
	
	$arSelect = Array("IBLOCK_ID", "ID");
	$arFilter = Array("IBLOCK_ID"=>14, "ACTIVE"=>"Y");
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement())
	{
	   $arFields = $ob->GetFields();
	   COptimus::DoIBlockAfterSave(array("ID"=>$arFields["ID"],"IBLOCK_ID"=>$arFields["IBLOCK_ID"]));
	   \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($arFields["IBLOCK_ID"], $arFields["ID"]);
	}	

	return 'setPrices();'; 
}

AddEventHandler('form', 'onAfterResultAdd', 'my_onAfterResultAddUpdate');
function my_onAfterResultAddUpdate($formId, $resultId) {
   if ($formId == 2)  {
      CFormResult::SetField($resultId, 'FIELD_PAGE_URL', 'http://'.SITE_SERVER_NAME.$GLOBALS['APPLICATION']->getCurPageParam());
   }
}

?>
