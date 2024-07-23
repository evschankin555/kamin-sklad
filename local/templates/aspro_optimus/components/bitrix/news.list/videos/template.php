<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if (count($arResult["ITEMS"])):?>	
<?if($arParams["AJAX_REQUEST"]=="N"){?>
	<div class="articles-list videos lists_block display_list <?=($arParams["IS_VERTICAL"]=="Y" ? "vertical row" : "")?> <?=($arParams["SHOW_FAQ_BLOCK"]=="Y" ? "faq" : "")?> ">
<?}?>    
		<?
			foreach($arResult["ITEMS"] as $arItem){
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arSize=array("WIDTH"=>280, "HEIGHT" => 190);
				if($arParams["SHOW_FAQ_BLOCK"]=="Y"){
					if($arParams["IS_VERTICAL"]!="Y")
						$arSize=array("WIDTH"=>175, "HEIGHT" => 120);
				}else{
					if($arParams["IS_VERTICAL"]!="Y")
						$arSize=array("WIDTH"=>190, "HEIGHT" => 130);
				}
		?>
			<div class="item clearfix item_block" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<div class="wrapper_inner_block">
				
					<?if($arItem["PREVIEW_PICTURE"]):?>
						<?$img = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array( "width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"] ), BX_RESIZE_IMAGE_EXACT, true );?>
						<div class="left-data">
							<a rel="category" href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" class="fb-img thumb"><img src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"] ? $arItem["PREVIEW_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"] ? $arItem["PREVIEW_PICTURE"]["TITLE"] : $arItem["NAME"])?>" /></a>
						</div>
					<?elseif($arItem["DETAIL_PICTURE"]):?>
						<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"] ), BX_RESIZE_IMAGE_EXACT, true );?>
						<div class="left-data">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb video_popup iframe"><img src="<?=$img["src"]?>" alt="<?=($arItem["DETAIL_PICTURE"]["ALT"] ? $arItem["DETAIL_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["DETAIL_PICTURE"]["TITLE"] ? $arItem["DETAIL_PICTURE"]["TITLE"] : $arItem["NAME"])?>" /></a>
						</div>
					<?else:?>
						<?
						$href = "//www.youtube.com/embed/".$arItem["PROPERTIES"]["VIDEO"]["VALUE"].'?autoplay=1';
						?>
						<div class="left-data">
							<a href="<?=$href?>" class="thumb video_popup iframe"><img src="//img.youtube.com/vi/<?=$arItem["DISPLAY_PROPERTIES"]["VIDEO"]["DISPLAY_VALUE"]?>/mqdefault.jpg" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>
						</div>
					<?endif;?>
					<div class="right-data">
						<?if($arParams["DISPLAY_DATE"]=="Y"){?>
							<?if( $arItem["PROPERTIES"]["PERIOD"]["VALUE"] ){?>
								<div class="date_small"><?=$arItem["PROPERTIES"]["PERIOD"]["VALUE"]?></div>
							<?}elseif($arItem["DISPLAY_ACTIVE_FROM"]){?>
								<div class="date_small"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
							<?}?>
						<?}?>
						<div class="item-title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a></div>
					</div>                    
					<div class="clear"></div>
				</div>
			</div>
		<?}?>
    <?if($arParams["AJAX_REQUEST"]=="N"){?>        
        </div>    
        <div class="clear"></div>    
    <?}?>    
    
	<?if($arParams["AJAX_REQUEST"]=="Y"){?>
		<div class="wrap_nav">
	<?}?>
	<div class="bottom_nav" <?=($arParams["AJAX_REQUEST"]=="Y" ? "style='display: none; '" : "");?>>
		<?if( $arParams["DISPLAY_BOTTOM_PAGER"] == "Y" ){?><?=$arResult["NAV_STRING"]?><?}?>
	</div>
	<?if($arParams["AJAX_REQUEST"]=="Y"){?>
		</div>
	<?}?>
         
<?endif;?>