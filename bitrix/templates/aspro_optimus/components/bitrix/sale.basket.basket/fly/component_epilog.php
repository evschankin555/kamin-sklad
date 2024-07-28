<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$detect = new \Bitrix\Conversion\Internals\MobileDetect;
if(!$detect->isMobile()){

$GLOBALS["simularCart"]=array();
foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):
	if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):	
		$arExpValues=COptimusCache::CIBlockElement_GetProperty(14, $arItem["PRODUCT_ID"], array("CACHE" => array("TAG" =>COptimusCache::GetIBlockCacheTag(14))), array("CODE" => "EXPANDABLES"));
		foreach ($arExpValues as $id):
			$GLOBALS["simularCart"][]=$id;
		endforeach;
	endif;
endforeach;
?>
<div class="simularCartFly">
<?
$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/comp_basket_bigdata.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);
?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.fly-basket-scroll').append($('.simularCartFly'));
		$('.simularCartFly').show();
	});
</script>
<?
}