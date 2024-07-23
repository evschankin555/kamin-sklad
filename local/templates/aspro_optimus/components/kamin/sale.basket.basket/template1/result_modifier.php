<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
use Bitrix\Main;

$defaultParams = array(
	'TEMPLATE_THEME' => 'blue'
);
$arParams = array_merge($defaultParams, $arParams);
unset($defaultParams);

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME'])
{
	$arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
	if ('site' == $arParams['TEMPLATE_THEME'])
	{
		$templateId = (string)Main\Config\Option::get('main', 'wizard_template_id', 'eshop_bootstrap', SITE_ID);
		$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? 'eshop_adapt' : $templateId;
		$arParams['TEMPLATE_THEME'] = (string)Main\Config\Option::get('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
	}
	if ('' != $arParams['TEMPLATE_THEME'])
	{
		if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
			$arParams['TEMPLATE_THEME'] = '';
	}
}
if ('' == $arParams['TEMPLATE_THEME'])
	$arParams['TEMPLATE_THEME'] = 'blue';
    
$arResult['ISHIDEDISCOUNT']=false;
foreach ($arResult["GRID"]["ROWS"] as $k => $arItem)
{
	$arDiscounts = CCatalogDiscount::GetDiscountByProduct(
        $arResult["GRID"]["ROWS"][$k]['PRODUCT_ID'],
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
			if (strpos($val2["NAME"],"HIDE")!==false) { $isHideDiscount=true; $arResult['ISHIDEDISCOUNT'] = true; break; }
			if ($val2["LAST_DISCOUNT"]=="Y") $isBreak=true;
			if ($isBreak) break;
		}
		if ($isBreak) break;
	}
	$arResult["GRID"]["ROWS"][$k]['ISHIDEDISCOUNT'] = $isHideDiscount;   
}    
