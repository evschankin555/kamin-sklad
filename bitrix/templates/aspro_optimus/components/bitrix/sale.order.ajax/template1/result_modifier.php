<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */
											
$arResult['JS_DATA']['ISHIDEDISCOUNT']=false;
foreach ($arResult['JS_DATA']["GRID"]["ROWS"] as $k => $arItem)
{
	$arDiscounts = CCatalogDiscount::GetDiscountByProduct(
        $arResult['JS_DATA']["GRID"]["ROWS"][$k]["data"]['PRODUCT_ID'],
        $USER->GetUserGroupArray(),
        "N",
        $_SESSION['CURRENT_LOCATION']['CURRENT']["PRICES"]["PRICE_ID"],
        SITE_ID
    );

	$isHideDiscount=false;
	$arDiscounts3=array();
	foreach($arDiscounts as $key => $val){
		$arDiscounts3[$val["PRIORITY"]][]=$val;
	}
	krsort($arDiscounts3);
	$isBreak=false;
	foreach($arDiscounts3 as $key => $val){
		foreach($val as $key2 => $val2){		
			if (strpos($val2["NAME"],"HIDE")!==false) { $isHideDiscount=true; $arResult['JS_DATA']['ISHIDEDISCOUNT'] = true; break; }
			if ($val2["LAST_DISCOUNT"]=="Y") $isBreak=true;
			if ($isBreak) break;
		}
		if ($isBreak) break;
	}

	$arResult['JS_DATA']["GRID"]["ROWS"][$k]["data"]['ISHIDEDISCOUNT'] = $isHideDiscount;   
	if ($isHideDiscount) {
		$arResult['JS_DATA']["GRID"]["ROWS"][$k]["data"]['DISCOUNT_PRICE_PERCENT'] = 0;
		$arResult['JS_DATA']["GRID"]["ROWS"][$k]["data"]['DISCOUNT_PRICE_PERCENT_FORMATED'] = '0%';
	}
}  

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);