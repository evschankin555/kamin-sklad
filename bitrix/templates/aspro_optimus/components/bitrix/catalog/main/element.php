<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");

// get current section & element

global $OptimusSectionID;
$arSection = $arElement = array();

// Создаем экземпляр класса COptimusCache
$optimusCacheInstance = new COptimusCache();

if ($arResult["VARIABLES"]["SECTION_ID"] > 0) {
    $arSection = $optimusCacheInstance->CIBlockSection_GetList(
        array('CACHE' => array("MULTI" => "N", "TAG" => $optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))),
        array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]),
        false,
        array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME")
    );
} elseif (strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0) {
    $arSection = $optimusCacheInstance->CIBlockSection_GetList(
        array('CACHE' => array("MULTI" => "N", "TAG" => $optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))),
        array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]),
        false,
        array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME")
    );
}

if ($arResult["VARIABLES"]["ELEMENT_ID"] > 0) {
    $arElement = $optimusCacheInstance->CIBLockElement_GetList(
        array('CACHE' => array("MULTI" => "N", "TAG" => $optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))),
        array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "ID" => $arResult["VARIABLES"]["ELEMENT_ID"]),
        false,
        false,
        array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_REGIONS")
    );
} elseif (strlen(trim($arResult["VARIABLES"]["ELEMENT_CODE"])) > 0) {
    $arElement = $optimusCacheInstance->CIBLockElement_GetList(
        array('CACHE' => array("MULTI" => "N", "TAG" => $optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))),
        array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"]),
        false,
        false,
        array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_REGIONS")
    );
}

if (!$arSection) {
    if ($arElement["IBLOCK_SECTION_ID"]) {
        $arSection = $optimusCacheInstance->CIBlockSection_GetList(
            array('CACHE' => array("MULTI" => "N", "TAG" => $optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))),
            array('GLOBAL_ACTIVE' => 'Y', "ID" => $arElement["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arElement["IBLOCK_ID"]),
            false,
            array("ID", "IBLOCK_ID", "UF_TIZERS", "NAME")
        );
    }
}

if (!empty($arElement["PROPERTY_REGIONS_VALUE"]))
{
	if (!is_array($arElement["PROPERTY_REGIONS_VALUE"])) $arElement["PROPERTY_REGIONS_VALUE"]=array(0=>$arElement["PROPERTY_REGIONS_VALUE"]);
	if (!in_array($_SESSION['CURRENT_LOCATION']['CURRENT']["ID"],$arElement["PROPERTY_REGIONS_VALUE"]))
	{
		if (!defined("ERROR_404"))
		   define("ERROR_404", "Y");
		
		\CHTTP::setStatus("404 Not Found");
		   
		if ($APPLICATION->RestartWorkarea()) {
		   require(\Bitrix\Main\Application::getDocumentRoot()."/404.php");
		   die();
		}
	}
}

$OptimusSectionID = $arSection["ID"];

global $TEMPLATE_OPTIONS;
?>

<?COptimus::AddMeta(
	array(
		'og:description' => $arElement['PREVIEW_TEXT'],
		'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
	)
);?>

<div class="catalog_detail" itemscope itemtype="https://schema.org/Product">
	<?$ElementID = $APPLICATION->IncludeComponent(
		"bitrix:catalog.element",
		"main",
		Array(      
			"SECTION_TIZER"=>$arSection["UF_TIZERS"],
			"SHOW_UNABLE_SKU_PROPS"=>$arParams["SHOW_UNABLE_SKU_PROPS"],
			"SHOW_DISCOUNT_TIME"=>$arParams["SHOW_DISCOUNT_TIME"],
			"TYPE_SKU" => $TEMPLATE_OPTIONS["TYPE_SKU"]["CURRENT_VALUE"],
			// "SKU_DETAIL_ID" => (isset($_GET[$arParams["SKU_DETAIL_ID"]]) && strlen($arParams["SKU_DETAIL_ID"]) && $TEMPLATE_OPTIONS["TYPE_SKU"]["CURRENT_VALUE"] == "TYPE_1" ? $_GET[$arParams["SKU_DETAIL_ID"]] : "" ),
			"SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
			"IBLOCK_REVIEWS_TYPE" => $arParams["IBLOCK_REVIEWS_TYPE"],
			"IBLOCK_REVIEWS_ID" => $arParams["IBLOCK_REVIEWS_ID"],
			"SHOW_ONE_CLICK_BUY" => $arParams["SHOW_ONE_CLICK_BUY"],
			"SEF_MODE_BRAND_SECTIONS" => $arParams["SEF_MODE_BRAND_SECTIONS"],
			"SEF_MODE_BRAND_ELEMENT" => $arParams["SEF_MODE_BRAND_ELEMENT"],
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
			"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
			"SET_LAST_MODIFIED" => "Y",
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"SHOW_404" => $arParams["SHOW_404"],
			"FILE_404" => $arParams["FILE_404"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
			"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
			"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
			"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
			"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],
			"USE_ALSO_BUY" => $arParams["USE_ALSO_BUY"],
			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"SKU_DISPLAY_LOCATION" => $arParams["SKU_DISPLAY_LOCATION"],
			"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
			"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
			"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
			"USE_STORE" => $arParams["USE_STORE"],
			"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
			"USE_STORE_SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
			"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
			"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
			"STORE_PATH" => $arParams["STORE_PATH"],
			"MAIN_TITLE" => $arParams["MAIN_TITLE"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"IBLOCK_STOCK_ID" => $arParams["IBLOCK_STOCK_ID"],
			"SEF_MODE_STOCK_SECTIONS" => $arParams["SEF_MODE_STOCK_SECTIONS"],
			"SHOW_QUANTITY" => $arParams["SHOW_QUANTITY"],
			"SHOW_QUANTITY_COUNT" => $arParams["SHOW_QUANTITY_COUNT"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
			'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
			"USE_ELEMENT_COUNTER" => $arParams["USE_ELEMENT_COUNTER"],
			'STRICT_SECTION_CHECK' => (isset($arParams['DETAIL_STRICT_SECTION_CHECK']) ? $arParams['DETAIL_STRICT_SECTION_CHECK'] : ''),
			'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),

			"USE_RATING" => $arParams["USE_RATING"],
			"USE_REVIEW" => $arParams["USE_REVIEW"],
			"FORUM_ID" => $arParams["FORUM_ID"],
			"MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
			"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
			"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
			"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
			"SHOW_BRAND_PICTURE" => $arParams["SHOW_BRAND_PICTURE"],
			"PROPERTIES_DISPLAY_LOCATION" => $arParams["PROPERTIES_DISPLAY_LOCATION"],
			"PROPERTIES_DISPLAY_TYPE" => $arParams["PROPERTIES_DISPLAY_TYPE"],
			"SHOW_ADDITIONAL_TAB" => $arParams["SHOW_ADDITIONAL_TAB"],
			"SHOW_ASK_BLOCK" => $arParams["SHOW_ASK_BLOCK"],
			"ASK_FORM_ID" => $arParams["ASK_FORM_ID"],
			"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
			"SHOW_HINTS" => $arParams["SHOW_HINTS"],
			"OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],
			"SHOW_KIT_PARTS" => $arParams["SHOW_KIT_PARTS"],
			"SHOW_KIT_PARTS_PRICES" => $arParams["SHOW_KIT_PARTS_PRICES"],
			"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
			"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
			'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
			"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
			"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
			"USER_FIELDS" => $arParams['USER_FIELDS'],
			"FIELDS" => $arParams['FIELDS'],
			"STORES" => $arParams['STORES'],
			"BIG_DATA_RCM_TYPE" => $arParams['BIG_DATA_RCM_TYPE'],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"SALE_STIKER" => $arParams["SALE_STIKER"],
			"SHOW_RATING" => $arParams["SHOW_RATING"],

			"OFFERS_LIMIT" => $arParams["DETAIL_OFFERS_LIMIT"],

			'SHOW_BASIS_PRICE' => (isset($arParams['DETAIL_SHOW_BASIS_PRICE']) ? $arParams['DETAIL_SHOW_BASIS_PRICE'] : 'Y'),
			"DETAIL_PICTURE_MODE" => (isset($TEMPLATE_OPTIONS["DETAIL_PICTURE_MODE"]["CURRENT_VALUE"]) ? $TEMPLATE_OPTIONS["DETAIL_PICTURE_MODE"]["CURRENT_VALUE"] : 'POPUP'),

			'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
			'SET_VIEWED_IN_COMPONENT' => (isset($arParams['DETAIL_SET_VIEWED_IN_COMPONENT']) ? $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'] : ''),

			'SHOW_SLIDER' => (isset($arParams['DETAIL_SHOW_SLIDER']) ? $arParams['DETAIL_SHOW_SLIDER'] : ''),
			'SLIDER_INTERVAL' => (isset($arParams['DETAIL_SLIDER_INTERVAL']) ? $arParams['DETAIL_SLIDER_INTERVAL'] : ''),
			'SLIDER_PROGRESS' => (isset($arParams['DETAIL_SLIDER_PROGRESS']) ? $arParams['DETAIL_SLIDER_PROGRESS'] : ''),
			'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
			'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),


			"USE_GIFTS_DETAIL" => $arParams['USE_GIFTS_DETAIL']?: 'Y',
			"USE_GIFTS_MAIN_PR_SECTION_LIST" => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST']?: 'Y',
			"GIFTS_SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
			"GIFTS_SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
			"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
			"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
			"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
			"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
			"GIFTS_SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
			"GIFTS_SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
			"GIFTS_MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

			"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
			"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],
		),
		$component
	);?>
</div>
<?COptimus::checkBreadcrumbsChain($arParams, $arSection, $arElement);?>
<div class="clearfix"></div>

<?/*fix title after ajax form start*/
$arAdditionalData = $arNavParams = array();
$arAdditionalData['TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle());
$arAdditionalData['WINDOW_TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle('title'));

// dirty hack: try to get breadcrumb call params
for ($i = 0, $cnt = count($APPLICATION->buffer_content_type); $i < $cnt; $i++){
	if ($APPLICATION->buffer_content_type[$i]['F'][1] == 'GetNavChain'){
		$arNavParams = $APPLICATION->buffer_content_type[$i]['P'];
	}
}
if ($arNavParams){
	$arAdditionalData['NAV_CHAIN'] = $APPLICATION->GetNavChain($arNavParams[0], $arNavParams[1], $arNavParams[2], $arNavParams[3], $arNavParams[4]);
}
?>
<script type="text/javascript">
	BX.addCustomEvent(window, "onAjaxSuccess", function(){
		var arAjaxPageData = <?=CUtil::PhpToJSObject($arAdditionalData);?>;
		if (arAjaxPageData.TITLE)
			BX.ajax.UpdatePageTitle(arAjaxPageData.TITLE);
		if (arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE)
			BX.ajax.UpdateWindowTitle(arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE);
		if (arAjaxPageData.NAV_CHAIN)
			BX.ajax.UpdatePageNavChain(arAjaxPageData.NAV_CHAIN);
		// top.BX.ajax.UpdatePageData(arAjaxPageData);
	});
</script>
<?/*fix title after ajax form end*/?>

<?$arAllValues=$arSimilar=$arAccessories=array();
/*similar goods*/
$arExpValues=$optimusCacheInstance->CIBlockElement_GetProperty($arParams["IBLOCK_ID"], $ElementID, array("CACHE" => array("TAG" =>$optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("CODE" => "EXPANDABLES"));
if($arExpValues){
	$arAllValues["EXPANDABLES"]=$arExpValues;
}
/*accessories goods*/
$arAccessories=$optimusCacheInstance->CIBlockElement_GetProperty($arParams["IBLOCK_ID"], $ElementID, array("CACHE" => array("TAG" =>$optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("CODE" => "ASSOCIATED"));
if($arAccessories){
	$arAllValues["ASSOCIATED"]=$arAccessories;
}
else
{
    global $simularArr;
    if ((float)$simularArr["MINIMUM_PRICE"]>0)
    {
        $arElements = $optimusCacheInstance->CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => $optimusCacheInstance->GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "!ID" => $ElementID, "SECTION_ID" => $simularArr["IBLOCK_SECTION_ID"], ">PROPERTY_MINIMUM_PRICE" => $simularArr["MINIMUM_PRICE"]*0.9,"<PROPERTY_MINIMUM_PRICE" => $simularArr["MINIMUM_PRICE"]*1.1), false, Array("nTopCount"=>12), array("ID"));               
        foreach($arElements as $sims):
	        $arAccessories[]=$sims["ID"];
            $arAllValues["ASSOCIATED"][]=$sims["ID"];
        endforeach;
    } 
}
?>

<?if($arAccessories || $arExpValues || (ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'))){?>
	<?$class_block="s_".$this->randString();
	$arTab=array();
	if($arExpValues){
		$arTab["EXPANDABLES"]=($arParams["DETAIL_EXPANDABLES_TITLE"] ? $arParams["DETAIL_EXPANDABLES_TITLE"] : GetMessage("EXPANDABLES_TITLE"));
	}
	if($arAccessories){
		$arTab["ASSOCIATED"]=( $arParams["DETAIL_ASSOCIATED_TITLE"] ? $arParams["DETAIL_ASSOCIATED_TITLE"] : GetMessage("ASSOCIATED_TITLE"));
	}
	/* Start Big Data */
	if(ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N')){
		$arTab["RECOMENDATION"]=GetMessage("RECOMENDATION_TITLE");
	}?>
	<div class="bottom_slider_ specials tab_slider_wrapp <?=$class_block;?>">
		<div class="top_blocks">
			<ul class="tabs">
				<?$i=1;
				foreach($arTab as $code=>$title):?>
					<li data-code="<?=$code?>" <?=($code=="RECOMENDATION" ? "style='display:none;'" : "" );?> <?=($i==1 ? "class='cur'" : "")?>><span><?=$title;?></span></li>
					<?$i++;?>
				<?endforeach;?>
				<li class="stretch"></li>
			</ul>
			<ul class="slider_navigation top custom_flex border">
				<?$i=1;
				foreach($arTab as $code=>$title):?>
					<li class="tabs_slider_navigation <?=$code?>_nav <?=($i==1 ? "cur" : "")?>" data-code="<?=$code?>"></li>
					<?$i++;?>
				<?endforeach;?>
			</ul>
		</div>

		<?$disply_elements=($arParams["DISPLAY_ELEMENT_SLIDER"] ? $arParams["DISPLAY_ELEMENT_SLIDER"] : 10);?>
		<ul class="tabs_content test">
			<?foreach($arTab as $code=>$title){?>
				<li class="tab <?=$code?>_wrapp" data-code="<?=$code?>">
					<?if($code=="RECOMENDATION"){?>
						<?
						$GLOBALS["CATALOG_CURRENT_ELEMENT_ID"] = $ElementID;
						?>
						<?$APPLICATION->IncludeComponent("bitrix:catalog.bigdata.products", COptimus::checkVersionExt(), array(
							"LINE_ELEMENT_COUNT" => 5,
							"TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_cbdp",
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"ADD_PROPERTIES_TO_BASKET" => "N",
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
							"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
							"SHOW_NAME" => "Y",
							"SHOW_IMAGE" => "Y",
							"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
							"SHOW_RATING" => $arParams["SHOW_RATING"],
							"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
							"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
							"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
							"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
							"PAGE_ELEMENT_COUNT" => $disply_elements,
							"SHOW_FROM_SECTION" => "N",
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"SALE_STIKER" => $arParams["SALE_STIKER"],
							"DEPTH" => "2",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
							"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => $arParams['ADD_PICT_PROP'],
							"LABEL_PROP_".$arParams["IBLOCK_ID"] => "-",
							"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
							'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
							"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"],
							"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"SECTION_ELEMENT_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"ID" => $ElementID,
							"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
							"CART_PROPERTIES_".$arParams["IBLOCK_ID"] => $arParams["PRODUCT_PROPERTIES"],
							"RCM_TYPE" => (isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : ''),
							"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
							),
							false,
							array("HIDE_ICONS" => "Y")
						);
						?>
					<?}else{?>
						<ul class="tabs_slider viewed_block_ <?=$code?>_slides wr">
							<?$GLOBALS['arrFilter'.$code] = array( "ID" => $arAllValues[$code] ); ?>      
                            
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"catalog_block_top", 
	array(
"SHOW_UNABLE_SKU_PROPS"=>$arParams["SHOW_UNABLE_SKU_PROPS"],
"TYPE_SKU" => $TEMPLATE_OPTIONS["TYPE_SKU"]["CURRENT_VALUE"],
"DISPLAY_TYPE" => 'block',
'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		"IBLOCK_TYPE" => "aspro_optimus_catalog",
		"IBLOCK_ID" => "14",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_COUNT" => "20",
		"LINE_ELEMENT_COUNT" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"PROPERTY_CODE" => array(
        0=>"BRAND"
		),
		"OFFERS_LIMIT" => "10",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"BASKET_URL" => SITE_DIR."basket/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"CACHE_GROUPS" => "N",
		"DISPLAY_COMPARE" => "Y",
        "PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_PROPERTIES" => array(
		),
		"CONVERT_CURRENCY" => "Y",
		"FILTER_NAME" => 'arrFilter'.$code,
		"SHOW_BUY_BUTTONS" => $arParams["SHOW_BUY_BUTTONS"],
		"USE_PRODUCT_QUANTITY" => "Y",
		"INIT_SLIDER" => "Y",
		"COMPONENT_TEMPLATE" => "catalog_block_slider",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"HIDE_NOT_AVAILABLE" => "N",
		"OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			3 => "CML2_LINK",
			4 => "DETAIL_PAGE_URL",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "ARTICLE",
			1 => "OTHER",
			2 => "COLOR_REF",
			3 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"SEF_MODE" => "N",
		"CACHE_FILTER" => "Y",
		"SHOW_MEASURE" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "OTHER",
			1 => "COLOR_REF",
		),
		"COMPARE_PATH" => "",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_RATING" => "N",
		"CURRENCY_ID" => "RUB",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_ALL_WO_SECTION" => "Y",
		"CUSTOM_FILTER" => "",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"PAGE_ELEMENT_COUNT" => "18",
		"BACKGROUND_IMAGE" => "-",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_LAST_MODIFIED" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"COMPATIBLE_MODE" => "Y",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N"
	),
	false, array("HIDE_ICONS"=>"Y")
);?>
						</ul>
					<?}?>
				</li>
			<?}?>
		</ul>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){

			$('.tab_slider_wrapp.<?=$class_block;?> .tabs > li').first().addClass('cur');
			$('.tab_slider_wrapp.<?=$class_block;?> .slider_navigation > li').first().addClass('cur');
			$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li').first().addClass('cur');

			var flexsliderItemWidth = 220;
			var flexsliderItemMargin = 12;

			var sliderWidth = $('.tab_slider_wrapp.<?=$class_block;?>').outerWidth();
			var flexsliderMinItems = Math.floor(sliderWidth / (flexsliderItemWidth + flexsliderItemMargin));
			$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').flexslider({
				animation: 'slide',
				selector: '.tabs_slider .catalog_item',
				slideshow: false,
				animationSpeed: 600,
				directionNav: true,
				controlNav: false,
				pauseOnHover: true,
				animationLoop: true,
				//easing: "",
				useCSS: false,
				itemWidth: flexsliderItemWidth,
				itemMargin: flexsliderItemMargin,
				//minItems: flexsliderMinItems,
				controlsContainer: '.tabs_slider_navigation.cur',
				start: function(slider){
					slider.find('li').css('opacity', 1);
				}
			});

			$('.tab_slider_wrapp.<?=$class_block;?> .tabs > li').on('click', function(){
				var sliderIndex = $(this).index();
				if(!$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur .flex-viewport').length){
					$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').flexslider({
						animation: 'slide',
						selector: '.tabs_slider .catalog_item',
						slideshow: false,
						animationSpeed: 600,
						directionNav: true,
						controlNav: false,
						pauseOnHover: true,
						animationLoop: true,
						//easing: "",
						useCSS: false,
						itemWidth: flexsliderItemWidth,
						itemMargin: flexsliderItemMargin,
						//minItems: flexsliderMinItems,
						controlsContainer: '.tabs_slider_navigation.cur',
					});
					setHeightBlockSlider();
				}
				if($(this).data("code")=="RECOMENDATION"){
					if($(this).data("clicked")!="Y"){
						$(window).resize();
						$(this).data("clicked","Y");
						setHeightBlockSlider();
					}
				}
			});

			setHeightBlockSlider();

			$(document).on({
				mouseover: function(e){
					var tabsContentHover = $(this).closest('.tab').attr('data-hover') * 1;
					$(this).closest('.tab').fadeTo(100, 1);
					$(this).closest('.tab').stop().css({'height': tabsContentHover});
					$(this).find('.buttons_block').fadeIn(450, 'easeOutCirc');
				},
				mouseleave: function(e){
					var tabsContentUnhoverHover = $(this).closest('.tab').attr('data-unhover') * 1;
					$(this).closest('.tab').stop().animate({'height': tabsContentUnhoverHover}, 100);
					$(this).find('.buttons_block').stop().fadeOut(233);
				}
			}, '.<?=$class_block;?> .tabs_slider > li');
		})
	</script>
<?}?>