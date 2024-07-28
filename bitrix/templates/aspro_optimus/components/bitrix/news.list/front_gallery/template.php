<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]){?>
<br>
			<?
			$title_block=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage('AKC_TITLE'));
			$title_all_block=($arParams["TITLE_BLOCK_ALL"] ? $arParams["TITLE_BLOCK_ALL"] : GetMessage('ALL_AKC'));
			$url=($arParams["ALL_URL"] ? $arParams["ALL_URL"] : "sale/");
			$count=ceil(count($arResult["ITEMS"])/4);
			?>
            <div class="title_block title_block2" style="margin-right: 38px;"><?=$title_block;?></div>
			<a href="<?=SITE_DIR.$url;?>"><?=$title_all_block;?></a>

		<div class="articles-list portf lists_block display_list <?=($arParams["IS_VERTICAL"]=="Y" ? "vertical row" : "")?> <?=($arParams["SHOW_FAQ_BLOCK"]=="Y" ? "faq" : "")?> ">
		<?
			foreach($arResult["ITEMS"] as $arItem){
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$arSize=array("WIDTH"=>224, "HEIGHT" => 227);
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
						<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"] ), BX_RESIZE_IMAGE_EXACT, true );?>
						<div class="left-data">
							<a rel="category" href="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>"  title="<?=($arItem["DETAIL_PICTURE"]["TITLE"] ? $arItem["DETAIL_PICTURE"]["TITLE"] : $arItem["NAME"])?>" class="fb-img thumb"><img src="<?=$img["src"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"] ? $arItem["PREVIEW_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"] ? $arItem["PREVIEW_PICTURE"]["TITLE"] : $arItem["NAME"])?>" /></a>
						</div>
					<?elseif($arItem["DETAIL_PICTURE"]):?>
						<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => $arSize["WIDTH"], "height" => $arSize["HEIGHT"] ), BX_RESIZE_IMAGE_EXACT, true );?>
						<div class="left-data">
	 						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"  title="<?=($arItem["DETAIL_PICTURE"]["TITLE"] ? $arItem["DETAIL_PICTURE"]["TITLE"] : $arItem["NAME"])?>" class="thumb"><img src="<?=$img["src"]?>" alt="<?=($arItem["DETAIL_PICTURE"]["ALT"] ? $arItem["DETAIL_PICTURE"]["ALT"] : $arItem["NAME"])?>" title="<?=($arItem["DETAIL_PICTURE"]["TITLE"] ? $arItem["DETAIL_PICTURE"]["TITLE"] : $arItem["NAME"])?>" /></a>
						</div>
					<?else:?>
						<div class="left-data">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" height="90" /></a>
						</div>
					<?endif;?>
					<div class="clear"></div>
				</div>
			</div>
		<?}?>
	</div>
<?}?>
    