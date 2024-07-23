<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>  
<div class="bottombanner">
<?global $SITE_THEME, $TEMPLATE_OPTIONS;?>
<?$APPLICATION->IncludeComponent(
	"aspro:com.banners.optimus", 
	"content_banner", 
	array(
		"IBLOCK_TYPE" => "aspro_optimus_adv",
		"IBLOCK_ID" => "2",
		"NEWS_ID" => $arResult["ID"],        
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

	<?if ($arParams["SHOW_LINKED_PRODUCTS"]=="Y" && $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"]):?>
		<hr class="long"/>
		<div class="similar_products_wrapp">
				<?if(CSite::InDir(SITE_DIR."sale")){?>
					<h3><?=GetMessage("ACTION_PRODUCTS");?></h3>
				<?}else{?>
					<h3><?=GetMessage("ACTION_PRODUCTS_LINK");?></h3>
				<?}?>
				<?if(!$arParams["CATALOG_FILTER_NAME"]){
					$arParams["CATALOG_FILTER_NAME"]="arrProductsFilter";
				}?>
				<div class="module-products-corusel product-list-items catalog block">                    
					<?$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array("ID" => $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"] );?>
					<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products.php');?>
				</div>
		</div>
	<?endif;?>

	<?if (!empty($arResult["TAGS"]) && $arResult["IBLOCK_ID"]==11):?>
         <div class="news__more news__more2">
				<hr class="long"/>         
                <div class="news__inner__block">
				<h3>Похожие новости</h3>  
				<?if(!$arParams["NEWS_FILTER_NAME"]){
					$arParams["NEWS_FILTER_NAME"]="arrNewsFilter";
				}?>
                
				<div class="articles-list lists_block news">
					<?
                        $arLooksLike = array(
                            "!ID"                 => intval($arResult["ID"])
                        );
                        $itemsArray = array();
                        foreach (array_map('trim', explode(',', $arResult["TAGS"])) as $item){
                            if (strlen($item) > 1){ // редко бывают значимые слова в одну букву
                                $itemsArray[] = array("TAGS" => "%".$item."%");
                            }
                        }
                        $addFArray = array(
                            array_merge(array("LOGIC" => "OR"), $itemsArray),
                        );
                        $GLOBALS[$arParams["NEWS_FILTER_NAME"]] = array_merge($arLooksLike, $addFArray);
                    ?>
					<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.news_list.php');?>
                    <a href="/news/?set_filter=Y&arrFilter_ff[TAGS]=<?=implode('|',array_map('trim', explode(',', $arResult["TAGS"])))?>" class="more">Еще новости по теме</a>
				</div>
                </div>
		</div>
	<?endif;?>
        
	<?if ($arParams["SHOW_SERVICES_BLOCK"]=="Y"):?>
		<div class="ask_big_block">
			<div class="ask_btn_block">
				<a class="button vbig_btn wides services_btn" data-title="<?=$arResult["NAME"];?>"><span><?=GetMessage("SERVICES_CALL")?></span></a>
			</div>
			<div class="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/services_block_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("ASK_QUESTION_DETAIL_TEXT"), ));?>
			</div>
			<div class="clear"></div>
		</div>
	<?endif;?>
    
<?if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?>   
<div class="news__more"> 
<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("form-feedback-block");?>
		<?$APPLICATION->IncludeComponent("bitrix:form.result.new", "inline",
			Array(
				"WEB_FORM_ID" => "3",
				"IGNORE_CUSTOM_TEMPLATE" => "N",
				"USE_EXTENDED_ERRORS" => "Y",
				"SEF_MODE" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "3600000",
				"LIST_URL" => "",
				"EDIT_URL" => "",
				"SUCCESS_URL" => "?send=ok",
				"CHAIN_ITEM_TEXT" => "",
				"CHAIN_ITEM_LINK" => "",
				"VARIABLE_ALIASES" => Array(
					"WEB_FORM_ID" => "WEB_FORM_ID",
					"RESULT_ID" => "RESULT_ID"
				)
			)
		);?>
<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("form-feedback-block", "");?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".FEEDBACK").find('[data-sid="NEWS"]').val('<?=str_replace("'","",$arResult["NAME"])?>');
	});
</script>   
<?endif;?>
    
<?if ($arParams["SHOW_BACK_LINK"]=="Y"):?>
	<?$refer=$_SERVER['HTTP_REFERER'];
	if (strpos($refer, $arResult["LIST_PAGE_URL"])!==false) {?>
		<div class="back"><a class="back" href="javascript:history.back();"><span><?=GetMessage("BACK");?></span></a></div>
	<?}else{?>
		<div class="back"><a class="back" href="<?=$arResult["LIST_PAGE_URL"]?>"><span><?=GetMessage("BACK");?></span></a></div>
	<?}?>
<?endif;?>