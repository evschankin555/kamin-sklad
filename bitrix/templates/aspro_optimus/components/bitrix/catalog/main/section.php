<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager;

Loader::includeModule("iblock");

// get current section ID
global $TEMPLATE_OPTIONS, $OptimusSectionID;
$arPageParams = $arSection = $section = array();

if($arResult["VARIABLES"]["SECTION_ID"] > 0){
	$section=COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "DESCRIPTION", "UF_SECTION_DESCR", "UF_SECTION_VIDEO","UF_TEXT2","UF_TEXT3", "UF_REGIONS", $arParams["SECTION_DISPLAY_PROPERTY"], "IBLOCK_SECTION_ID"));
}
elseif(strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0){

	$section=COptimusCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "DESCRIPTION", "UF_SECTION_DESCR", "UF_SECTION_VIDEO", "UF_TEXT2","UF_TEXT3", "UF_REGIONS", $arParams["SECTION_DISPLAY_PROPERTY"], "IBLOCK_SECTION_ID"));
}

if($section){
	$arSection["ID"] = $section["ID"];
	$arSection["NAME"] = $section["NAME"];
	$arSection["IBLOCK_SECTION_ID"] = $section["IBLOCK_SECTION_ID"];
	if($section[$arParams["SECTION_DISPLAY_PROPERTY"]]){
		$arDisplayRes = CUserFieldEnum::GetList(array(), array("ID" => $section[$arParams["SECTION_DISPLAY_PROPERTY"]]));
		if($arDisplay = $arDisplayRes->GetNext()){
			$arSection["DISPLAY"] = $arDisplay["XML_ID"];
		}
	}
	if(strlen($section["DESCRIPTION"]))
		$arSection["DESCRIPTION"] = $section["DESCRIPTION"];
	if(strlen($section["UF_SECTION_DESCR"]))
		$arSection["UF_SECTION_DESCR"] = $section["UF_SECTION_DESCR"];
	if(strlen($section["UF_SECTION_VIDEO"]))
		$arSection["UF_SECTION_VIDEO"] = $section["UF_SECTION_VIDEO"];
	if(strlen($section["UF_TEXT2"]))
		$arSection["UF_TEXT2"] = $section["UF_TEXT2"];
	if(strlen($section["UF_TEXT3"]))
		$arSection["UF_TEXT3"] = $section["UF_TEXT3"];
	if(!empty($section["UF_REGIONS"]))
		$arSection["UF_REGIONS"] = $section["UF_REGIONS"];
	$posSectionDescr = COption::GetOptionString("aspro.optimus", "SHOW_SECTION_DESCRIPTION", "BOTTOM", SITE_ID);
	
	$iSectionsCount = COptimusCache::CIBlockSection_GetCount(array('CACHE' => array("TAG" => COptimusCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("SECTION_ID" => $arSection["ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y"));
}

if (!empty($arSection["UF_REGIONS"]))
{
	if (!in_array($_SESSION['CURRENT_LOCATION']['CURRENT']["ID"],$arSection["UF_REGIONS"]))
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

$OptimusSectionID = $arSection["ID"];?>
<div class="topbanner"></div>
<?$APPLICATION->IncludeComponent(
	"kamin:catalog.section.list",
	"subsections_list",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"COUNT_ELEMENTS" => "N",
		"ADD_SECTIONS_CHAIN" => ((!$iSectionsCount || $arParams['INCLUDE_SUBSECTIONS'] !== "N") ? 'N' : 'Y'),
		"SHOW_SECTIONS_LIST_PREVIEW" => $arParams["SHOW_SECTIONS_LIST_PREVIEW"],
		"SECTIONS_LIST_PREVIEW_PROPERTY" => $arParams["SECTIONS_LIST_PREVIEW_PROPERTY"],
		"SHOW_SECTION_LIST_PICTURES" => $arParams["SHOW_SECTION_PICTURES"],
		"SECTIONS_LIST_PREVIEW_DESCRIPTION" => $arParams["SECTION_PREVIEW_DESCRIPTION"],
		"TOP_DEPTH" => "1",
        "REGIONS" => $_SESSION['CURRENT_LOCATION']['CURRENT']["ID"]
	),
	$component
);?>
<?if(!$iSectionsCount || $arParams['INCLUDE_SUBSECTIONS'] !== "N"):?>
	<?if($TEMPLATE_OPTIONS["TYPE_VIEW_FILTER"]["CURRENT_VALUE"]=="VERTICAL"){?>
		<?ob_start()?>
			<?include_once("filter.php")?>
		<?$html=ob_get_clean();?>
		<?$APPLICATION->AddViewContent('left_menu', $html);?>
	<?}?>
	<div class="right_block1 clearfix catalog <?=strtolower($TEMPLATE_OPTIONS["TYPE_VIEW_FILTER"]["CURRENT_VALUE"]);?>" id="right_block_ajax">

		<?$isAjax="N";?>
		<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"  && isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y" || (isset($_GET["ajax_basket"]) && $_GET["ajax_basket"]=="Y")){
			$isAjax="Y";
		}?>
		<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest" && isset($_GET["ajax_get_filter"]) && $_GET["ajax_get_filter"] == "Y" ){
			$isAjaxFilter="Y";
		}?>
        
		<?if($isAjax=="N"){?>     
				<?if($posSectionDescr=="BOTH"):?>
					<?if($arSection[$section_pos_bottom]):?>
						<div class="group_description_block bottom">
							<div><?=$arSection[$section_pos_bottom]?></div>
						</div>
					<?endif;?>
				<?elseif($posSectionDescr=="BOTTOM"):?> 
					<?if($arSection["UF_TEXT2"]):?>
						<div class="group_description_block bottom">
							<div><?=$arSection["UF_TEXT2"]?></div>
						</div>
					<?endif;?> 
					<?if($arSection["UF_SECTION_VIDEO"]):?>
						<div class="group_description_block bottom">
							<div><iframe style="border:0;"  src="https://www.youtube.com/embed/<?=$arSection["UF_SECTION_VIDEO"]?>"></iframe></div>
						</div>
					<?else:?>
						<div class="group_description_block bottom">
							<!-- <div><iframe src="https://www.youtube.com/embed/SuLbGd6XoEI"></iframe></div> -->
							<iframe style="width:560px;height:315px;border:0;" src="https://www.youtube.com/embed/SuLbGd6XoEI" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>
					<?endif;?>    
                                 
					    
				<?endif;?>
		<?}?>
                    <?if('Y' == $arParams['USE_FILTER']):?>
				<div class="adaptive_filter">
					<a class="filter_opener<?=($_REQUEST["set_filter"] == "y" ? " active" : "")?>"><i></i><span><?=GetMessage("CATALOG_SMART_FILTER_TITLE")?></span></a>
				</div>
				<script type="text/javascript">
				$(".filter_opener").click(function(){
					$(this).toggleClass("opened");
					$(".bx_filter_vertical, .bx_filter").slideToggle(333);
				});
				</script>
			<?endif;?>
		<?if($TEMPLATE_OPTIONS["TYPE_VIEW_FILTER"]["CURRENT_VALUE"]=="HORIZONTAL"){?>
			<div class="filter_horizontal">
				<?include_once("filter.php")?>
			</div>
		<?}else{?>
			<div class="js_filter filter_horizontal">
				<div class="bx_filter bx_filter_vertical"></div>
			</div>
		<?}?>
		<?$section_pos_top = \Bitrix\Main\Config\Option::get("aspro.optimus", "TOP_SECTION_DESCRIPTION_POSITION", "UF_SECTION_DESCR", SITE_ID );?>
		<?$section_pos_bottom = \Bitrix\Main\Config\Option::get("aspro.optimus", "BOTTOM_SECTION_DESCRIPTION_POSITION", "DESCRIPTION", SITE_ID );?>
		<div class="inner_wrapper">

			

			<?if($isAjax=="N"){
				$frame = new \Bitrix\Main\Page\FrameHelper("viewtype-block");
				$frame->begin();?>
			<?}?>
			<div class="sm-filter-sort-container">
				<?include_once("sort.php");?>
			</div>

			<?if($isAjax=="Y"){
				$APPLICATION->RestartBuffer();
			}?>
			<?$show = $arParams["PAGE_ELEMENT_COUNT"];?>
			<?if($isAjax=="N"){?>
				<div class="ajax_load <?=$display;?>">
			<?} 
            ?>
				<?
        global $OPTIMUS_SMART_FILTER;

       // print_r($OPTIMUS_SMART_FILTER);
        $APPLICATION->IncludeComponent(
					"bitrix:catalog.section",
					$template,
					Array(
						"SHOW_UNABLE_SKU_PROPS"=>$arParams["SHOW_UNABLE_SKU_PROPS"],
						"SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
						"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
						"AJAX_REQUEST" => $isAjax,
						"ELEMENT_SORT_FIELD2" => $sort,
						"ELEMENT_SORT_ORDER2" => $sort_order,
						"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER2"],
						"FILTER_NAME" => $arParams["FILTER_NAME"],
						"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
						"PAGE_ELEMENT_COUNT" => $show,
						"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
						"DISPLAY_TYPE" => $display,
						"TYPE_SKU" => $TEMPLATE_OPTIONS["TYPE_SKU"]["CURRENT_VALUE"],
						"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],

						"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],

						"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

						"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
						"BASKET_URL" => $arParams["BASKET_URL"],
						"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						"PRODUCT_QUANTITY_VARIABLE" => "quantity",
						"PRODUCT_PROPS_VARIABLE" => "prop",
						"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
						"AJAX_MODE" => $arParams["AJAX_MODE"],
						"AJAX_OPTION_JUMP" => $arParams["AJAX_OPTION_JUMP"],
						"AJAX_OPTION_STYLE" => $arParams["AJAX_OPTION_STYLE"],
						"AJAX_OPTION_HISTORY" => $arParams["AJAX_OPTION_HISTORY"],
						"CACHE_TYPE" =>"N",
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"CACHE_FILTER" => "Y",
						"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
						"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
						"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
						"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
						"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
						'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
						"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
						"SET_TITLE" => $arParams["SET_TITLE"],
						"SET_STATUS_404" => $arParams["SET_STATUS_404"],
						"SHOW_404" => $arParams["SHOW_404"],
						"MESSAGE_404" => $arParams["MESSAGE_404"],
						"FILE_404" => $arParams["FILE_404"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
						"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],

						"PAGER_TITLE" => $arParams["PAGER_TITLE"],
						"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
						"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
						"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
						"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
						"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

						"AJAX_OPTION_ADDITIONAL" => "",
						"ADD_CHAIN_ITEM" => "N",
						"SHOW_QUANTITY" => $arParams["SHOW_QUANTITY"],
						"SHOW_QUANTITY_COUNT" => $arParams["SHOW_QUANTITY_COUNT"],
						"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
						"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
						"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"USE_STORE" => $arParams["USE_STORE"],
						"MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
						"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
						"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
						"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
						"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
						"LIST_DISPLAY_POPUP_IMAGE" => $arParams["LIST_DISPLAY_POPUP_IMAGE"],
						"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						"SHOW_HINTS" => $arParams["SHOW_HINTS"],
						"OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],
						"SHOW_SECTIONS_LIST_PREVIEW" => $arParams["SHOW_SECTIONS_LIST_PREVIEW"],
						"SECTIONS_LIST_PREVIEW_PROPERTY" => $arParams["SECTIONS_LIST_PREVIEW_PROPERTY"],
						"SHOW_SECTION_LIST_PICTURES" => $arParams["SHOW_SECTION_LIST_PICTURES"],
						"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
						"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
						"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
						"SALE_STIKER" => $arParams["SALE_STIKER"],
						"SHOW_RATING" => $arParams["SHOW_RATING"],
					), $component, array("HIDE_ICONS" => $isAjax)
				);?>
			<?if($isAjax=="N"){?>

<!--section url::
<?
//print_r($arResult);
?>
-->

<? ob_start(); ?>	                        
					<?if($arSection[$arParams["SECTION_PREVIEW_PROPERTY"]]):?>
						<div class="group_description_block bottom">
							<div><?
global $sotbitSeoMetaBottomDesc;
if(!empty($sotbitSeoMetaBottomDesc))
{
	echo $sotbitSeoMetaBottomDesc;
}
else
{
	echo $arSection[$arParams["SECTION_PREVIEW_PROPERTY"]];
}								?>
							</div>
						</div>
					<?elseif ($arSection["DESCRIPTION"]):?>
						<div class="group_description_block bottom">
							<div><?=$arSection["DESCRIPTION"]?></div>
						</div>
					<?elseif($arSection["UF_SECTION_DESCR"]):?>
						<div class="group_description_block bottom">
							<div><?=$arSection["UF_SECTION_DESCR"]?></div>
						</div>
					<?endif;?>
					<?
                    $desc = ob_get_clean();                
                    if (strlen($desc)>300) echo('<div id="cat-text-top" style="clear: both;">'.$desc.'</div><a id="cat-text-open">Подробнее</a>');
                    else echo($desc);
                    ?>                  

					<?if($arSection["UF_TEXT3"]):?>
						<div class="group_description_block bottom">
							<div><?=$arSection["UF_TEXT3"]?></div>
						</div>
					<?endif;?>             
				<div class="clear"></div>
				</div>
			<?}?>
			<?if($isAjax!="Y"){?>
				<?$frame->end();?>
			<?}?>
			<?if($isAjax=="Y"){
				die();
			}?>

<?global $SITE_THEME, $TEMPLATE_OPTIONS;?>
<?$APPLICATION->IncludeComponent(
	"aspro:com.banners.optimus", 
	"content-banner-catalog", 
	array(
		"IBLOCK_TYPE" => "aspro_optimus_adv",
		"IBLOCK_ID" => "2",
		"CATALOG_ID" => $arSection["ID"],        
		"TYPE_BANNERS_IBLOCK_ID" => "3598",
		"SET_BANNER_TYPE_FROM_THEME" => "N",
		"NEWS_COUNT" => "1",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "ID",
		"SORT_ORDER2" => "DESC",
		"PROPERTY_CODE" => array(
			0 => "TEXT_POSITION",
			1 => "TARGETS",
			2 => "TEXTCOLOR",
			3 => "URL_STRING",
			4 => "BUTTON1TEXT",
			5 => "BUTTON1LINK",
			6 => "BUTTON2TEXT",
			7 => "BUTTON2LINK",
			8 => "",
		),
		"CHECK_DATES" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"SITE_THEME" => $SITE_THEME,
		"BANNER_TYPE_THEME" => "NEWS"
	),
	false
);?>

		</div>
	</div>
<?else:?>
	<?
	$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], IntVal($arSection["ID"]));
	$arValues = $ipropValues->getValues();
	if($arParams["SET_TITLE"] !== 'N'){
		$page_h1 = $arValues['SECTION_PAGE_TITLE'] ? $arValues['SECTION_PAGE_TITLE'] : $arSection["NAME"];
		if($page_h1){
			$APPLICATION->SetTitle($page_h1);
		}
		else{
			$APPLICATION->SetTitle($arSection["NAME"]);
		}
	}
	$page_title = $arValues['SECTION_META_TITLE'] ? $arValues['SECTION_META_TITLE'] : $arSection["NAME"];
	if($page_title){
		$APPLICATION->SetPageProperty("title", $page_title);
	}
	if($arValues['SECTION_META_DESCRIPTION']){
		$APPLICATION->SetPageProperty("description", $arValues['SECTION_META_DESCRIPTION']);
	}
	if($arValues['SECTION_META_KEYWORDS']){
		$APPLICATION->SetPageProperty("keywords", $arValues['SECTION_META_KEYWORDS']);
	}
	?>
<?endif;?>
<?COptimus::checkBreadcrumbsChain($arParams, $arSection);?>

<?
$APPLICATION->AddHeadString('<link href="https://'.$_SERVER["HTTP_HOST"] .$arResult['FOLDER'].$arResult['VARIABLES']['SECTION_CODE_PATH'] . '/" rel="canonical" />',true);
echo "<!--SERVER: " . $_SERVER["HTTP_HOST"] . '-->';
echo "<!--SITE_SERVER_NAME: " . SITE_SERVER_NAME . '-->';

?>

<?//sotbit seometa meta start
global $sotbitSeoMetaTitle;
global $sotbitSeoMetaKeywords;
global $sotbitSeoMetaDescription;
global $sotbitSeoMetaBreadcrumbTitle;
global $sotbitSeoMetaH1;
if(!empty($sotbitSeoMetaH1))
{
$APPLICATION->SetTitle($sotbitSeoMetaH1);
}
if(!empty($sotbitSeoMetaTitle))
{
$APPLICATION->SetPageProperty("title", $sotbitSeoMetaTitle);
}
if(!empty($sotbitSeoMetaKeywords))
{
$APPLICATION->SetPageProperty("keywords", $sotbitSeoMetaKeywords);
}
if(!empty($sotbitSeoMetaDescription))
{
$APPLICATION->SetPageProperty("description", $sotbitSeoMetaDescription);
}
if(!empty($sotbitSeoMetaBreadcrumbTitle) ) {
$APPLICATION->AddChainItem($sotbitSeoMetaBreadcrumbTitle  );
}
//sotbit seometa meta end ?>