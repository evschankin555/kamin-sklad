<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>


<div class="news_detail_wrapp article big item">
<div class="catalog_detail">
		<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => 'SOCIAL'));?>
	</div>
	<div class="article__top">
    	<? if (strlen($arResult["PROPERTIES"]["AUTHOR"]["VALUE"])>0) { ?>
		<div class="avtor">Автор: <?=$arResult["PROPERTIES"]["AUTHOR"]["VALUE"]?></div>
        <? } ?>
		<div class="date">
				<?if($arParams["DISPLAY_DATE"]!="N"):?>

			<?if($arResult["PROPERTIES"][$arParams["PERIOD_PROPERTY"]]["VALUE"]):?>
				<?=$arResult["PROPERTIES"][$arParams["PERIOD_PROPERTY"]]["VALUE"];?>
			<?elseif($arResult["DISPLAY_ACTIVE_FROM"]):?>
				<?=$arResult["DISPLAY_ACTIVE_FROM"]?>
			<?endif;?>
			<?endif;?>
		</div>
		<div class="prosmotrov"><?=$arResult["SHOW_COUNTER"]?></div>
		<div class="komments"><?=$arResult["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]?></div>	

						<?if($arParams["USE_RATING"] == "Y"):?>
							
								<?$frame = $this->createFrame('dv_'.$arResult["ID"])->begin('');?>
									<div class="rating" style="margin-left: 20px;max-width: 120px;margin-top: -6px;">
										<?$APPLICATION->IncludeComponent(
										   "bitrix:iblock.vote",
										   "element_rating",
										   Array(
											  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											  "IBLOCK_ID" => $arResult["IBLOCK_ID"],
											  "ELEMENT_ID" => $arResult["ID"],
											  "MAX_VOTE" => 5,
											  "VOTE_NAMES" => array(),
											  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
											  "CACHE_TIME" => $arParams["CACHE_TIME"],
											  "DISPLAY_AS_RATING" => 'vote_avg'
										   ),
										   $component, array("HIDE_ICONS" =>"Y")
										);?>
									</div>
								<?$frame->end();?>
							<?endif;?>
		
	</div>

	<?if ($arParams["SHOW_LINKED_PRODUCTS"]=="Y" && $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"]):?>
	    <hr class="long"/>
	    <div class="similar_products_wrapp">
	            
	            <?if(!$arParams["CATALOG_FILTER_NAME"]){
	                $arParams["CATALOG_FILTER_NAME"]="arrProductsFilter";
	            }?>
	            <div class="module-products-corusel product-list-items catalog">                    
	                <?$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array("ID" => $arResult["DISPLAY_PROPERTIES"][$arParams["LINKED_PRODUCTS_PROPERTY"]]["VALUE"] );?>
	                <?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_slider.php');?>
	            </div>
	    </div>
	<?endif;?>
	
	<!--
	<?if( !empty($arResult["DETAIL_PICTURE"])):?>
		<div class="detail-art_picture_block clearfix">
			
			
				<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=($arResult["DETAIL_PICTURE"]["ALT"] ? $arResult["DETAIL_PICTURE"]["ALT"] : $arResult["NAME"])?>" title="<?=($arResult["DETAIL_PICTURE"]["TITLE"] ? $arResult["DETAIL_PICTURE"]["TITLE"] : $arResult["NAME"])?>"/>
			<div class="socs catalog_detail" style="width: auto; background-color: #fff; padding: 2px; padding-bottom: 6px;">
				<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"  data-yashareLink="http://<?=$_SERVER["HTTP_HOST"]?><?=$arResult["DETAIL_PICTURE"]["SRC"]?>" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,gplus"></div> 
            </div>
		</div>
	<?endif;?>
	-->
    
	<?if (strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO"]["DISPLAY_VALUE"])>0):?>
	    <iframe allowfullscreen="" frameborder="0" width="100%" height="516" src="//www.youtube.com/embed/<?=$arResult["DISPLAY_PROPERTIES"]["VIDEO"]["DISPLAY_VALUE"]?>" ></iframe><br>
	<?endif;?>    

		
	<?if ($arResult["DETAIL_TEXT"]):?>
<div class="detail_text <?=($arResult["DETAIL_PICTURE"] ? "wimg" : "");?>">
<?
$str=str_replace("class=\"fb-img-content\"","class=\"fb-img-content\" rel=\"gallery\"",$arResult["DETAIL_TEXT"]);

$doc = new DOMDocument();
//$doc->loadHtml(mb_convert_encoding($str, 'HTML-ENTITIES', 'WINDOWS-1251'));
$doc->loadHtml(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));

$xpath = new DOMXPath($doc);
$tags  = $xpath->query(
    '//*[img/parent::a or (self::img and not(parent::a))]'
);

foreach ($tags as $tag) {
	    $div = $doc->createElement('div');
    $div->setAttribute("class","detail-art_picture_block clearfix");
    $div->setAttribute("style","display:inline-block");
    $tag->parentNode->insertBefore($div, $tag);
    $div->appendChild($tag);
$src = $tag->getAttribute('src');
if ($src=='') $src = $tag->getAttribute('href');
$f = $doc->createDocumentFragment();
$f->appendXML('<div class="socs catalog_detail" style="width: auto; background-color: #fff; padding: 2px; padding-bottom: 6px;"><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none"  data-yashareLink="http://'.$_SERVER["HTTP_HOST"].$src.'" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,gplus"></div></div>');
$div->appendChild($f);
}

echo $doc->saveHTML();
?>
</div>
	<?endif;?>
	
	
	<?/*if($arParams["SHOW_PRICE"]=="Y" && $arResult["PROPERTIES"][$arParams["PRICE_PROPERTY"]]["VALUE"]):?>
		<div class="price_block">
			<div class="price"><?=GetMessage("SERVICE_PRICE")?> <?=$arResult["PROPERTIES"][$arParams["PRICE_PROPERTY"]]["VALUE"];?></div>
		</div>
	<?endif;*/?>

	<div class="clear"></div>
	<?if($arParams["SHOW_GALLERY"]=="Y" && $arResult["PROPERTIES"][$arParams["GALLERY_PROPERTY"]]["VALUE"]){?>
		<div class="row galley">
			<ul class="module-gallery-list">
				<?
					$files = $arResult["PROPERTIES"][$arParams["GALLERY_PROPERTY"]]["VALUE"];		
					if(array_key_exists('SRC', $files)) 
					{
					   $files['SRC'] = '/' . substr($files['SRC'], 1);
					   $files['ID'] = $arResult["PROPERTIES"][$arParams["GALLERY_PROPERTY"]]["VALUE"][0];
					   $files = array($files);
					}
				?>
				<?	foreach($files as $arFile):?>
					<?					
						$img = CFile::ResizeImageGet($arFile, array('width'=>450, 'height'=>338), BX_RESIZE_IMAGE_EXACT, true);						
						$img_big = CFile::ResizeImageGet($arFile, array('width'=>800, 'height'=>600), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						$img_big = $img_big['src'];		
						$arFileBig = CFile::GetFileArray($arFile);			
					?>
					<li class="item_block">
						<a href="<?=$img_big;?>" class="fancy" data-fancybox-group="gallery">
							<img src="<?=$img["src"];?>" alt="<?=$arFileBig["DESCRIPTION"];?>" title="<?=$arFileBig["DESCRIPTION"];?>" />
						</a>				  
							<div class="name"><?=$arFileBig["DESCRIPTION"];?></div>
					</li>
				<?endforeach;?>
			</ul>
			<div class="clear"></div>
		</div>
	<?}?>

	<?
    $arr = explode(", ", $arResult["TAGS"]);
    if (count($arr) > 0 && strlen($arResult["TAGS"]) > 0) {
        echo('<div class="block tag">');
        foreach ($arr as $c):
	        echo('<a href="../?set_filter=Y&arrFilter_ff[TAGS]=' . $c . '" class="button small transparent grey_br">' . $c . '</a> ');
        endforeach;
        echo('</div><br />');
    }
    ?>

	<?/*if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?>
		<div class="ask_big_block">
			<div class="ask_btn_block">
				<a class="button vbig_btn wides ask_btn"><span><?=GetMessage("ASK_QUESTION")?></span></a>
			</div>
			<div class="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/ask_block_detail_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("ASK_QUESTION_DETAIL_TEXT"), ));?>
			</div>
		</div>
	<?endif;*/?>
</div>
