<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
Bitrix\Main\Loader::includeModule('sale');

$arDefaultParams = array(
	'TEMPLATE_THEME' => 'blue',
);
$arParams = array_merge($arDefaultParams, $arParams);

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME'])
{
	$arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
	if ('site' == $arParams['TEMPLATE_THEME'])
	{
		$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
		$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
		$arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
	}
	if ('' != $arParams['TEMPLATE_THEME'])
	{
		if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
			$arParams['TEMPLATE_THEME'] = '';
	}
}
if ('' == $arParams['TEMPLATE_THEME'])
	$arParams['TEMPLATE_THEME'] = 'blue';

if ($arResult["ELEMENT"]['DETAIL_PICTURE'] || $arResult["ELEMENT"]['PREVIEW_PICTURE'])
{
	$arFileTmp = CFile::ResizeImageGet(
		$arResult["ELEMENT"]['DETAIL_PICTURE'] ? $arResult["ELEMENT"]['DETAIL_PICTURE'] : $arResult["ELEMENT"]['PREVIEW_PICTURE'],
		array("width" => "450", "height" => "540"),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);
	$arResult["ELEMENT"]['DETAIL_PICTURE_SMALL'] = $arFileTmp;
	$arFileTmp = CFile::ResizeImageGet(
		$arResult["ELEMENT"]['DETAIL_PICTURE'] ? $arResult["ELEMENT"]['DETAIL_PICTURE'] : $arResult["ELEMENT"]['PREVIEW_PICTURE'],
		array("width" => "1000", "height" => "1000"),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);
	$arResult["ELEMENT"]['DETAIL_PICTURE'] = $arFileTmp;
}

$arResult["isDISCOUNT"]=false;
$arDefaultSetIDs = array($arResult["ELEMENT"]["ID"]);
$arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"]=0;
$arResult['SET_ITEMS']['PRICE'] = 0;
$arResult['SET_ITEMS']['OLD_PRICE'] = 0;

$arOrder = array(
'SITE_ID' => SITE_ID,
'USER_ID' => $GLOBALS["USER"]->GetID(),
'ORDER_PRICE' => "0", // сумма всей корзины
'ORDER_WEIGHT' => "0", // вес всей корзины
'BASKET_ITEMS' => array(
   array(
	  'PRODUCT_ID' => $arResult["ELEMENT"]["ID"], 
	  'PRODUCT_PRICE_ID' => 0, 
	  'PRICE' => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 
	  'BASE_PRICE' => $arResult["ELEMENT"]["PRICE_VALUE"], 
	  'QUANTITY' => '1.0000', 
	  'LID' => SITE_ID,
	  'MODULE' => 'catalog', 
   )
   )
);
$arOptions = array( 
'COUNT_DISCOUNT_4_ALL_QUANTITY' => "Y", 
); 
$arErrors = array(); 
CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
foreach ($arOrder["BASKET_ITEMS"] as $value)
{
	if ($value["PRODUCT_ID"]==$arResult["ELEMENT"]["ID"])
	{				
			$arResult["ELEMENT"]["PRICE_ONE_DISCOUNT_VALUE"]=$value["PRICE"];
			$arResult["ELEMENT"]["PRICE_ONE_VALUE"]=$value["BASE_PRICE"];
			$arResult["ELEMENT"]["PRICE_ONE_DISCOUNT_DIFFERENCE_VALUE"]=$value["BASE_PRICE"]-$value["PRICE"];                        
	}                    			
}
						
foreach (array("DEFAULT", "OTHER") as $type)
{
	foreach ($arResult["SET_ITEMS"][$type] as $key=>$arItem)
	{            
		$arElement = array(
			"ID"=>$arItem["ID"],
			"NAME" =>$arItem["NAME"],
			"DETAIL_PAGE_URL"=>$arItem["DETAIL_PAGE_URL"],
			"DETAIL_PICTURE"=>$arItem["DETAIL_PICTURE"],
			"PREVIEW_PICTURE"=> $arItem["PREVIEW_PICTURE"],
			"PRICE_CURRENCY" => $arItem["PRICE_CURRENCY"],
			"PRICE_DISCOUNT_VALUE" => $arItem["PRICE_DISCOUNT_VALUE"],
			"PRICE_PRINT_DISCOUNT_VALUE" => $arItem["PRICE_PRINT_DISCOUNT_VALUE"],
			"PRICE_DISCOUNT_DIFFERENCE_VALUE" => $arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"],
			"PRICE_DISCOUNT_DIFFERENCE" => $arItem["PRICE_DISCOUNT_DIFFERENCE"],						
			"PRICE_VALUE" => $arItem["PRICE_VALUE"],
			"PRICE_PRINT_VALUE" => $arItem["PRICE_PRINT_VALUE"],					
			"CAN_BUY" => $arItem['CAN_BUY'],
			"SET_QUANTITY" => $arItem['SET_QUANTITY'],
			"MEASURE_RATIO" => $arItem['MEASURE_RATIO'],
			"BASKET_QUANTITY" => $arItem['BASKET_QUANTITY'],
			"MEASURE" => $arItem['MEASURE']
		);
		if ($type=="DEFAULT") $arResult['SET_ITEMS']['OLD_PRICE'] += $arItem["PRICE_VALUE"]*$arItem['BASKET_QUANTITY'];
		
		if ($arItem["PRICE_CONVERT_DISCOUNT_VALUE"])
			$arElement["PRICE_CONVERT_DISCOUNT_VALUE"] = $arItem["PRICE_CONVERT_DISCOUNT_VALUE"];
		if ($arItem["PRICE_CONVERT_VALUE"])
			$arElement["PRICE_CONVERT_VALUE"] = $arItem["PRICE_CONVERT_VALUE"];
		if ($arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"])
			$arElement["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"] = $arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"];

        $arOrder = array(
        'SITE_ID' => SITE_ID,
        'USER_ID' => $GLOBALS["USER"]->GetID(),
        'ORDER_PRICE' => "0", // сумма всей корзины
        'ORDER_WEIGHT' => "0", // вес всей корзины
        'BASKET_ITEMS' => array(
           array(
              'PRODUCT_ID' => $arResult["ELEMENT"]["ID"], 
              'PRODUCT_PRICE_ID' => 0, 
              'PRICE' => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 
              'BASE_PRICE' => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 
              'QUANTITY' => '1.0000', 
              'LID' => SITE_ID,
              'MODULE' => 'catalog', 
           ),
           array(
              'PRODUCT_ID' => $arItem["ID"], 
              'PRODUCT_PRICE_ID' => 0, 
              'PRICE' => $arItem["PRICE_DISCOUNT_VALUE"], 
              'BASE_PRICE' => $arItem["PRICE_DISCOUNT_VALUE"], 
              'QUANTITY' => $arItem['BASKET_QUANTITY'], 
              'LID' => SITE_ID, 
              'MODULE' => 'catalog', 
           )
           )
        );
	
        $arOptions = array( 
        'COUNT_DISCOUNT_4_ALL_QUANTITY' => "Y", 
        ); 
        $arErrors = array(); 
        CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
        //Bitrix\Main\Diag\Debug::dump($arOrder);
        //Bitrix\Main\Diag\Debug::dump($arOptions);
        //Bitrix\Main\Diag\Debug::dump($arErrors);
        //exit();
        
        
        foreach ($arOrder["BASKET_ITEMS"] as $value)
        {
            if ($value["PRODUCT_ID"]==$arItem["ID"])
            {
                if ($value["DISCOUNT_PRICE_PERCENT"]>0)
                {
                    $arElement["PRICE_DISCOUNT_VALUE"] = $value["PRICE"];
                    $arElement["PRICE_PRINT_DISCOUNT_VALUE"] = ceil($value["PRICE"]).' <span class="price_currency"></span>';
                    $arElement["PRICE_DISCOUNT_DIFFERENCE"] = $value["BASE_PRICE"]-$value["PRICE"];
                    $arElement["PRICE_DISCOUNT_DIFFERENCE_VALUE"] = $value["BASE_PRICE"]-$value["PRICE"];
					
					$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE'] += $arElement['PRICE_DISCOUNT_DIFFERENCE_VALUE']*$arElement['BASKET_QUANTITY'];
                    $arResult["isDISCOUNT"]=true;
                }
            }
            if ($value["PRODUCT_ID"]==$arResult["ELEMENT"]["ID"] && $value["PRICE"]<$arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"])
            {		
                if ($value["DISCOUNT_PRICE_PERCENT"]>0)
                {				
                    $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] = $value["PRICE"];
                    $arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"] = ceil($value["PRICE"]).' <span class="price_currency"></span>';
                    $arResult["ELEMENT"]["PRICE_VALUE"] = $value["BASE_PRICE"];					
//                    $arResult["ELEMENT"]["PRICE_PRINT_VALUE"] = $value["BASE_PRICE"];					
                    $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"] = $value["BASE_PRICE"]-$value["PRICE"];                        
					
					$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE'] += $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"]*$arResult["ELEMENT"]['BASKET_QUANTITY'];
					$arResult["isDISCOUNT"]=true;
				}
            }                    			
        }
		
		if ($type=="DEFAULT") $arResult['SET_ITEMS']['PRICE'] += $arElement["PRICE_DISCOUNT_VALUE"]*$arElement["BASKET_QUANTITY"];
    
		if ($type == "DEFAULT")
			$arDefaultSetIDs[] = $arItem["ID"];
		if ($arItem['DETAIL_PICTURE'] || $arItem['PREVIEW_PICTURE'])
		{
			$arFileTmp = CFile::ResizeImageGet(
				$arItem['DETAIL_PICTURE'] ? $arItem['DETAIL_PICTURE'] : $arItem['PREVIEW_PICTURE'],
				array("width" => "450", "height" => "540"),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arElement['DETAIL_PICTURE_SMALL'] = $arFileTmp;
			$arFileTmp = CFile::ResizeImageGet(
				$arItem['DETAIL_PICTURE'] ? $arItem['DETAIL_PICTURE'] : $arItem['PREVIEW_PICTURE'],
				array("width" => "1000", "height" => "1000"),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arElement['DETAIL_PICTURE'] = $arFileTmp;
		}

		$arResult["SET_ITEMS"][$type][$key] = $arElement;
	}
}

if (!$arResult["isDISCOUNT"])
{	
	$arOrder = array(
	'SITE_ID' => SITE_ID,
	'USER_ID' => $GLOBALS["USER"]->GetID(),
	'ORDER_PRICE' => "0", // сумма всей корзины
	'ORDER_WEIGHT' => "0", // вес всей корзины
	'BASKET_ITEMS' => array(
	   array(
		  'PRODUCT_ID' => $arResult["ELEMENT"]["ID"], 
		  'PRODUCT_PRICE_ID' => 0, 
		  'PRICE' => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 
		  'BASE_PRICE' => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 
		  'QUANTITY' => '1.0000', 
		  'LID' => SITE_ID,
		  'MODULE' => 'catalog', 
	   )
	   )
	);			
	foreach (array("DEFAULT", "OTHER") as $type)
	{
		foreach ($arResult["SET_ITEMS"][$type] as $key=>$arItem)
		{  		
				$arOrder["BASKET_ITEMS"][]=array(
				'PRODUCT_ID' => $arItem["ID"], 
				'PRODUCT_PRICE_ID' => 0, 
				'PRICE' => $arItem["PRICE_DISCOUNT_VALUE"], 
				'BASE_PRICE' => $arItem["PRICE_DISCOUNT_VALUE"], 
				'QUANTITY' => $arItem['BASKET_QUANTITY'], 
				'LID' => SITE_ID, 
				'MODULE' => 'catalog', 
				);
		}
	}
			   
	$arOptions = array( 
	'COUNT_DISCOUNT_4_ALL_QUANTITY' => "Y", 
	); 
	$arErrors = array(); 
	CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);		

	$arDefaultSetIDs = array($arResult["ELEMENT"]["ID"]);
	$arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"]=0;
	$arResult['SET_ITEMS']['PRICE'] = 0;
	$arResult['SET_ITEMS']['OLD_PRICE'] = 0;
	foreach (array("DEFAULT", "OTHER") as $type)
	{
	foreach ($arResult["SET_ITEMS"][$type] as $key=>$arItem)
	{		
		$db_props = CIBlockElement::GetProperty($arItem["IBLOCK_ID"], $arItem["ID"], array("sort" => "asc"), Array("CODE"=>"NAIMENOVANIE_DLYA_WEB_FS_GB_"));
		if($ar_props = $db_props->Fetch())
		{
			$arItem["NAME"]=$ar_props["VALUE_ENUM"]." ".$arItem["NAME"];
		}
	    
		$arElement = array(
			"ID"=>$arItem["ID"],
			"NAME" =>$arItem["NAME"],
			"DETAIL_PAGE_URL"=>$arItem["DETAIL_PAGE_URL"],
			"DETAIL_PICTURE"=>$arItem["DETAIL_PICTURE"],
			"PREVIEW_PICTURE"=> $arItem["PREVIEW_PICTURE"],
			"PRICE_CURRENCY" => $arItem["PRICE_CURRENCY"],
			"PRICE_DISCOUNT_VALUE" => $arItem["PRICE_DISCOUNT_VALUE"],
			"PRICE_PRINT_DISCOUNT_VALUE" => $arItem["PRICE_PRINT_DISCOUNT_VALUE"],
			"PRICE_DISCOUNT_DIFFERENCE_VALUE" => $arItem["PRICE_DISCOUNT_DIFFERENCE_VALUE"],
			"PRICE_DISCOUNT_DIFFERENCE" => $arItem["PRICE_DISCOUNT_DIFFERENCE"],
			"PRICE_VALUE" => $arItem["PRICE_VALUE"],
			"PRICE_PRINT_VALUE" => $arItem["PRICE_PRINT_VALUE"],
			"CAN_BUY" => $arItem['CAN_BUY'],
			"SET_QUANTITY" => $arItem['SET_QUANTITY'],
			"MEASURE_RATIO" => $arItem['MEASURE_RATIO'],
			"BASKET_QUANTITY" => $arItem['BASKET_QUANTITY'],
			"MEASURE" => $arItem['MEASURE']
		);
		if ($type=="DEFAULT") $arResult['SET_ITEMS']['OLD_PRICE'] += $arItem["PRICE_VALUE"]*$arItem['BASKET_QUANTITY'];
		
		if ($arItem["PRICE_CONVERT_DISCOUNT_VALUE"])
			$arElement["PRICE_CONVERT_DISCOUNT_VALUE"] = $arItem["PRICE_CONVERT_DISCOUNT_VALUE"];
		if ($arItem["PRICE_CONVERT_VALUE"])
			$arElement["PRICE_CONVERT_VALUE"] = $arItem["PRICE_CONVERT_VALUE"];
		if ($arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"])
			$arElement["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"] = $arItem["PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE"];
					
        foreach ($arOrder["BASKET_ITEMS"] as $value)
        {
            if ($value["PRODUCT_ID"]==$arItem["ID"])
            {
                if ($value["DISCOUNT_PRICE_PERCENT"]>0)
                {
                    $arElement["PRICE_DISCOUNT_VALUE"] = $value["PRICE"];
                    $arElement["PRICE_PRINT_DISCOUNT_VALUE"] = SaleFormatCurrency($value["PRICE"], $arItem["PRICE_CURRENCY"]);
                    $arElement["PRICE_DISCOUNT_DIFFERENCE"] = $value["BASE_PRICE"]-$value["PRICE"];
                    $arElement["PRICE_DISCOUNT_DIFFERENCE_VALUE"] = $value["BASE_PRICE"]-$value["PRICE"];
					
					$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE'] += $arElement['PRICE_DISCOUNT_DIFFERENCE_VALUE']*$arElement['BASKET_QUANTITY'];
                    $arResult["isDISCOUNT"]=true;
                }
            }
            if ($value["PRODUCT_ID"]==$arResult["ELEMENT"]["ID"] && $value["PRICE"]<$arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"])
            {		
                if ($value["DISCOUNT_PRICE_PERCENT"]>0)
                {				
                    $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] = $value["PRICE"];
                    $arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"] = SaleFormatCurrency($value["PRICE"], $arResult["ELEMENT"]["PRICE_CURRENCY"]);
                    $arResult["ELEMENT"]["PRICE_VALUE"] = $value["BASE_PRICE"];					
//                    $arResult["ELEMENT"]["PRICE_PRINT_VALUE"] = $value["BASE_PRICE"];					
                    $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"] = $value["BASE_PRICE"]-$value["PRICE"];                        
					
					$arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE'] += $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"]*$arResult["ELEMENT"]['BASKET_QUANTITY'];
					$arResult["isDISCOUNT"]=true;
				}
            }                    			
        }
		
		if ($type=="DEFAULT") $arResult['SET_ITEMS']['PRICE'] += $arElement["PRICE_DISCOUNT_VALUE"]*$arElement["BASKET_QUANTITY"];		
    
		if ($type == "DEFAULT")
			$arDefaultSetIDs[] = $arItem["ID"];

		$arResult["SET_ITEMS"][$type][$key] = $arElement;		
	}
	}
}

$arResult['SET_ITEMS']['PRICE'] += $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"];
$arResult['SET_ITEMS']['OLD_PRICE'] += $arResult["ELEMENT"]["PRICE_VALUE"];

$defaultCurrency = $arResult['ELEMENT']['PRICE_CURRENCY'];
if ($arResult["SET_ITEMS"]["OLD_PRICE"] && $arResult["SET_ITEMS"]["OLD_PRICE"] != $arResult["SET_ITEMS"]["PRICE"])
	$arResult["SET_ITEMS"]["OLD_PRICE"] = strip_tags(CCurrencyLang::CurrencyFormat($arResult["SET_ITEMS"]["OLD_PRICE"], $defaultCurrency));
else
	$arResult["SET_ITEMS"]["OLD_PRICE"] = 0;
if ($arResult["SET_ITEMS"]["PRICE"])
	$arResult["SET_ITEMS"]["PRICE"] = strip_tags(CCurrencyLang::CurrencyFormat($arResult["SET_ITEMS"]["PRICE"], $defaultCurrency));
if ($arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"])
	$arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"] = strip_tags(CCurrencyLang::CurrencyFormat($arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"], $defaultCurrency));
		
$arResult["DEFAULT_SET_IDS"] = $arDefaultSetIDs;
