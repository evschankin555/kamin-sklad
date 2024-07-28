<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$GLOBALS["simularCart"]=array();
foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):
	if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):	
		$arExpValues=COptimusCache::CIBlockElement_GetProperty(14, $arItem["PRODUCT_ID"], array("CACHE" => array("TAG" =>COptimusCache::GetIBlockCacheTag(14))), array("CODE" => "EXPANDABLES"));
		foreach ($arExpValues as $id):
			$GLOBALS["simularCart"][]=$id;
		endforeach;
	endif;
endforeach;
