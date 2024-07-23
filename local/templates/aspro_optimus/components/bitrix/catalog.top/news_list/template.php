<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<? $this->setFrameMode( true ); ?>
<?
$sliderID  = "specials_slider_wrapp_".$this->randString();
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
<?if($arResult["ITEMS"]):?>
<div class="items">
<?
	foreach($arResult["ITEMS"] as $key => $arItem):
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<div class="item clearfix item_block" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<div class="wrapper_inner_block">
				<?if($arItem["PREVIEW_PICTURE"]):?>
					<?$img = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array( "width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"] ), BX_RESIZE_IMAGE_EXACT, true );?>
					<div class="left-data">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"] ? $arItem["PREVIEW_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"] ? $arItem["PREVIEW_PICTURE"]["TITLE"] : $arItem["NAME"])?>" /></a>
					</div>
				<?elseif($arItem["DETAIL_PICTURE"]):?>
					<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"] ), BX_RESIZE_IMAGE_EXACT, true );?>
					<div class="left-data">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arItem["DETAIL_PICTURE"]["ALT"] ? $arItem["DETAIL_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["DETAIL_PICTURE"]["TITLE"] ? $arItem["DETAIL_PICTURE"]["TITLE"] : $arItem["NAME"])?>" /></a>
					</div>
				<?else:?>
					<div class="left-data">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" height="90" /></a>
					</div>
				<?endif;?>
				<div class="right-data">
                    <?if($arItem["DATE_ACTIVE_FROM"]){?>
                        <div class="date_small"><?=FormatDate("j F Y", MakeTimeStamp($arItem["DATE_ACTIVE_FROM"]))?></div>
                    <?}?>
					<div class="item-title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a></div>
					<div class="preview-text"><?=$arItem["PREVIEW_TEXT"]?></div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
<?
	endforeach;
?>
</div>						
<?else:?>
	<?$this->setFrameMode(true);?>
	<script type="text/javascript">
	$(document).ready(function(){
		$(".news_detail_wrapp .news__more2").remove();
	}); /* dirty hack, remove this code */
	</script>
<?endif;?>