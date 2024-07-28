<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>   
<div class="bottombanner">
    <?global $SITE_THEME, $TEMPLATE_OPTIONS;?>
    <?$APPLICATION->IncludeComponent(
        "aspro:com.banners.optimus", 
        "content-banner-article", 
        array(
            "IBLOCK_TYPE" => "aspro_optimus_adv",
            "IBLOCK_ID" => "2",
            "ARTICLES_ID" => $arResult["ID"],
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
                    <div class="module-products-corusel product-list-items catalog">                    
                        <?$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array("ID" => $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"] );?>
                        <?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_slider.php');?>
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
        
<h3>Вам понравился материал?</h3>
<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons2.php", Array(), Array("MODE" => "html", "NAME" => 'SOCIAL'));?>

                    
    <?if(IsModuleInstalled("forum") && $arResult["ID"]):?>
    <?echo($arParams["PATH_TO_SMILE"]);?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:forum.topic.reviews",
        "main_articles",
        Array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "MESSAGES_PER_PAGE" => 5,
            "USE_CAPTCHA" => "Y",
            "PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
            "FORUM_ID" => "2",
            "URL_TEMPLATES_READ" => "",
            "SHOW_LINK_TO_FORUM" => "N",     
            "DATE_TIME_FORMAT" => "j F Y",
            "ELEMENT_ID" => $arResult["ID"],
            "AJAX_POST" => "Y",
            "IBLOCK_ID" => $arResult["IBLOCK_ID"],
            "URL_TEMPLATES_DETAIL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
            "SHOW_RATING" => "N",
            "SHOW_MINIMIZED" => "Y",
            "SECTION_REVIEW" => "Y",
            "POST_FIRST_MESSAGE" => "Y",
            "MINIMIZED_MINIMIZE_TEXT" => "Свернуть форму",
            "MINIMIZED_EXPAND_TEXT" => "Добавить комментарий",
            "SHOW_AVATAR" => "N",
            "SHOW_LINK_TO_FORUM" => "N",        
        ),
        $component
    );?>
    <?endif?>
   
<?if (!empty($arResult["TAGS"])):?>
<div class="more_articles">
    <?if(!$arParams["ARTICLES_FILTER_NAME"]){
        $arParams["ARTICLES_FILTER_NAME"]="arrArticlesFilter";
    }?>
	<?
		$isAjax="N";
		if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"  && isset($_GET["ajax_get"]) && $_GET["ajax_get"] == "Y" || (isset($_GET["ajax_basket"]) && $_GET["ajax_basket"]=="Y")){
			$isAjax="Y";
		}
			
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
        $GLOBALS[$arParams["ARTICLES_FILTER_NAME"]] = array_merge($arLooksLike, $addFArray);		
    ?>    
<?if($isAjax=="N"){?>     
    <h3>Похожие статьи:</h3>
<?}?>    
    <?$APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_template_art",
        Array(
            "IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
            "AJAX_REQUEST" => $isAjax,
            "IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
            'INCLUDE_SUBSECTIONS' =>'Y',
            "IS_VERTICAL"	=>	'Y',
            "SHOW_FAQ_BLOCK"	=>	"N",
            "NEWS_COUNT"	=>	3,
            "SORT_BY1" => "ACTIVE_FROM",
            "SORT_ORDER1" => "DESC",
            "SORT_BY2" => "SORT",
            "SORT_ORDER2" => "ASC",
            "LIST_FIELD_CODE" => array(
                0 => "DETAIL_PICTURE",
                1 => "",
            ),
            "SET_TITLE"	=>	"N",
            "SET_STATUS_404" => "N",
            "CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
            "CACHE_TIME"	=>	$arParams["CACHE_TIME"],
            "CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "DISPLAY_BOTTOM_PAGER"	=>	"Y",
            "PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
            "PAGER_TEMPLATE"	=>	"main",
            "PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
            "DISPLAY_NAME"	=>	"Y",
            "DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
            "DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
            "PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
            "ACTIVE_DATE_FORMAT"	=>	"j F Y",
            "USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
            "GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
            "FILTER_NAME"	=>	$arParams["ARTICLES_FILTER_NAME"],
            "HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
            "CHECK_DATES"	=>	$arParams["CHECK_DATES"],
            "SHOW_FAQ_BLOCK" => "N",
            "SHOW_BACK_LINK" => "N",
        ),
        $component
    );?>
</div>    
<?endif;?>






