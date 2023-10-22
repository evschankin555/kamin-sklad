<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["SECTIONS"]){?>
<div class="wrapper_inner1 wides float_banners">
<div class="start_promo other">
<?$i=1;?>
	<?foreach( $arResult["SECTIONS"] as $arItems ){?>
			<?
			$this->AddEditAction($arItems['ID'], $arItems['EDIT_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItems['ID'], $arItems['DELETE_LINK'], CIBlock::GetArrayByID($arItems["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			$isUrl=(strlen($arItems["SECTION_PAGE_URL"]) ? true : false);

			$type="";
$rsGender = CUserFieldEnum::GetList(array(), array("ID" => $arItems["UF_PIC_SIZE"]));
        if ($arCat = $rsGender->GetNext())
            $type=$arCat["XML_ID"];			
			?>
			<?if($arItems["PICTURE"]["SRC"]):?>
				<div class="item s_<?=$i;?> <?=($isUrl ? "hover" : "");?> <?=($type ? $type : "normal");?>" id="<?=$this->GetEditAreaId($arItems['ID']);?>">
					<?$arItems["FORMAT_NAME"]=strip_tags($arItems["NAME"]);?>
					<?if($isUrl){?>
						<a href="<?=$arItems["SECTION_PAGE_URL"]?>" class="opacity_block1 dark_block_animate" title="<?=$arItems["FORMAT_NAME"];?>"></a>
					<?}?>
						<div class="wrap_tizer  left_blocks light_text">
							<div class="wrapper_inner_tizer">
								<div class="wr_block">
									<span class="wrap_outer title">
										<?if($isUrl){?>
											<a class="outer_text" href="<?=$arItems["SECTION_PAGE_URL"]?>">
										<?}else{?>
											<span class="outer_text">
										<?}?>
											<span class="inner_text">
												<?=strip_tags($arItems["NAME"], "<br><br/>");?>
											</span>
										<?if($isUrl){?>
											</a>
										<?}else{?>
											</span>
										<?}?>
									</span>
								</div>
								<?if($arItems["DESCRIPTION"]){?>
									<div class="wr_block price">
										<span class="wrap_outer_desc">
											<?if($isUrl){?>
												<a class="outer_text_desc" href="<?=$arItems["SECTION_PAGE_URL"]?>">
											<?}else{?>
												<span class="outer_text_desc">
											<?}?>
												<span class="inner_text_desc">
													<?=trim(strip_tags($arItems["DESCRIPTION"]))?>
												</span>
											<?if($isUrl){?>
												</a>
											<?}else{?>
												</span>
											<?}?>
										</span>
									</div>
								<?}?>
							</div>
						</div>

					<?if($isUrl){?>
						<a href="<?=$arItems["SECTION_PAGE_URL"]?>">
					<?}?>
						<img class="scale_block_animate" src="<?=$arItems["PICTURE"]["SRC"]?>" alt="<?=$arItems["FORMAT_NAME"]?>" title="<?=$arItems["FORMAT_NAME"]?>" />
					<?if($isUrl){?>
						</a>
					<?}?>
				</div>
				<?$i++;?>
			<?endif;?>
	<?}?>
<div class="clearfix"></div>    
</div>
</div>
<?}?>