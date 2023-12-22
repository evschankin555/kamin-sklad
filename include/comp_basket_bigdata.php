<?
$arParams["CATALOG_FILTER_NAME"]="simularCart";
$arParams["SHOW_BUY_BUTTONS"]="Y";
?>

<?if (!empty($GLOBALS["simularCart"])): ?>
<div class="similar_products_wrapp test" style="position:relative;">
	<h3>Сопутствующие товары</h3>
	<?$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array( "ID" => $GLOBALS["simularCart"] );?>
    <div class="similar_products_wrapp" style="position:relative;">    
        <?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_slider.php');?>
    </div>    
</div>
<?endif;?>

<?/*$APPLICATION->IncludeComponent(
	"bitrix:catalog.bigdata.products", 
	"optimus_new", 
	array(
		"LINE_ELEMENT_COUNT" => "5",
		"TEMPLATE_THEME" => "blue",
		"DETAIL_URL" => "",
        "TITLE_BLOCK"=>"Вам также могут пригодиться",
		"BASKET_URL" => SITE_DIR."basket/",
		"ACTION_VARIABLE" => "ACTION",
		"PRODUCT_ID_VARIABLE" => "ID",
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"ADD_PROPERTIES_TO_BASKET" => "N",
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"PRICE_CODE" => array(
			0 => $_SESSION['CURRENT_LOCATION']['CURRENT']["PRICES"]["PRICE"],1 => "BASE"
		),
		"SHOW_PRICE_COUNT" => "1",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PRICE_VAT_INCLUDE" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"SHOW_NAME" => "Y",
		"SHOW_IMAGE" => "Y",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
		"PAGE_ELEMENT_COUNT" => "20",
		"SHOW_FROM_SECTION" => "N",
		"IBLOCK_TYPE" => "aspro_optimus_catalog",
		"IBLOCK_ID" => "14",
		"DEPTH" => "2",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "N",
		"HIDE_NOT_AVAILABLE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"CURRENCY_ID" => $arParams["CURRENCY_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_ELEMENT_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"ID" => "",
		"={\"PROPERTY_CODE_\".\$arParams[\"IBLOCK_ID\"]}" => $arParams["LIST_PROPERTY_CODE"],
		"={\"CART_PROPERTIES_\".\$arParams[\"IBLOCK_ID\"]}" => $arParams["PRODUCT_PROPERTIES"],
		"RCM_TYPE" => "bestsell",
		"={\"OFFER_TREE_PROPS_\".\$ElementOfferIblockID}" => $arParams["OFFER_TREE_PROPS"],
		"={\"ADDITIONAL_PICT_PROP_\".\$ElementOfferIblockID}" => $arParams["OFFER_ADD_PICT_PROP"],
		"COMPONENT_TEMPLATE" => "optimus_new",
		"SHOW_PRODUCTS_14" => "Y",
		"PROPERTY_CODE_14" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_14" => array(
		),
		"ADDITIONAL_PICT_PROP_14" => "MORE_PHOTO",
		"LABEL_PROP_14" => "-",
		"PROPERTY_CODE_15" => array(
			0 => "",
			1 => "",
		),
		"CART_PROPERTIES_15" => array(
			0 => "undefined",
		),
		"ADDITIONAL_PICT_PROP_15" => "MORE_PHOTO",
		"OFFER_TREE_PROPS_15" => array(
		),
		"DISPLAY_COMPARE" => "Y",
		"SHOW_RATING" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);*/
?>