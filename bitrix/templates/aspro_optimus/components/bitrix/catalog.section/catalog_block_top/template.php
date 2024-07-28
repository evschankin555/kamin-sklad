<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){
	$injectId = 'sale_gift_product_'.$this->randString();?>
					<?
					$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
					$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
					$elementDeleteParams = array('CONFIRM' => GetMessage('CVP_TPL_ELEMENT_DELETE_CONFIRM'));
					?>
					<?foreach($arResult['ITEMS'] as $key => $arItem){
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $elementEdit);
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $elementDelete, $elementDeleteParams);
						$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
						$arItemIDs=COptimus::GetItemsIDs($arItem);

						$strMeasure = '';
						$totalCount = COptimus::GetTotalCount($arItem);
						$arQuantityData = COptimus::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"]);
						if(!$arItem["OFFERS"]){
							if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
								$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
								$strMeasure = $arMeasure["SYMBOL_RUS"];
							}
							$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
						}
						elseif($arItem["OFFERS"]){
							$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
							if(!$arItem['OFFERS_PROP']){

								$arAddToBasketData = COptimus::GetAddToBasketArray($arItem["OFFERS"][0], $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
							}
						}
						?>
                        
<?if($arItem["ISHIDEDISCOUNT"]){?>
<input type="hidden" id="ISHIDEDISCOUNT_<?=$arItem["ID"]?>" value="1" />
<? } ?>
                        
						<li class="catalog_item" id="<?=$arItem["strMainID"];?>">
							<div class="image_wrapper_block">
								<div class="stickers">
									<?if (is_array($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"])):?>
										<?foreach($arItem["PROPERTIES"]["HIT"]["VALUE_XML_ID"] as $key=>$class){?>
											<div><div class="sticker_<?=strtolower($class);?>"><?=$arItem["PROPERTIES"]["HIT"]["VALUE"][$key]?></div></div>
										<?}?>
									<?endif;?>
									<?if($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
										<div><div class="sticker_sale_text"><?=$arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
									<?}?>
								</div>
								<?if((!$arItem["OFFERS"] && $arParams["DISPLAY_WISH_BUTTONS"] != "N" ) || ($arParams["DISPLAY_COMPARE"] == "Y")):?>
									<div class="like_icons">
										<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
											<?if(!$arItem["OFFERS"]):?>
												<div class="wish_item_button">
													<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
													<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
												</div>
											<?elseif($arItem["OFFERS"] && !empty($arItem['OFFERS_PROP'])):?>
												<div class="wish_item_button" style="display: none;">
													<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
													<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
												</div>
											<?endif;?>
										<?endif;?>
										<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
											<?if(!$arItem["OFFERS"] || $arParams["TYPE_SKU"] !== 'TYPE_1'):?>
												<div class="compare_item_button">
													<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
													<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
												</div>
											<?elseif($arItem["OFFERS"]):?>
												<div class="compare_item_button">
													<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="" ><i></i></span>
													<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item=""><i></i></span>
												</div>
											<?endif;?>
										<?endif;?>
									</div>
								<?endif;?>
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>">
									<?
									$a_alt=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] );
									$a_title=($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] );
									?>
									<?if( !empty($arItem["PREVIEW_PICTURE"]) ):?>
										<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
									<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
										<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 170, "height" => 170 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
										<img src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
									<?else:?>
										<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
									<?endif;?>
								</a>
							</div>
							<div class="item_info <?=$arParams["TYPE_SKU"]?>">
								<div class="item-title">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                                    <?if(isset($arItem['DISPLAY_PROPERTIES']['BRAND']) && $arItem['DISPLAY_PROPERTIES']['BRAND']){?>
                                        <?=strip_tags($arItem["DISPLAY_PROPERTIES"]['BRAND']['DISPLAY_VALUE']);?><br>
                                    <?}?>  
                                    <span><?=$arItem["NAME"]?></span></a>
								</div>
								<?if($arParams["SHOW_RATING"] == "Y"):?>
									<div class="rating">
										<?$APPLICATION->IncludeComponent(
										   "bitrix:iblock.vote",
										   "element_rating_front",
										   Array(
											  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											  "IBLOCK_ID" => $arItem["IBLOCK_ID"],
											  "ELEMENT_ID" =>$arItem["ID"],
											  "MAX_VOTE" => 5,
											  "VOTE_NAMES" => array(),
											  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
											  "CACHE_TIME" => $arParams["CACHE_TIME"],
											  "DISPLAY_AS_RATING" => 'vote_avg'
										   ),
										   $component, array("HIDE_ICONS" =>"Y")
										);?>
									</div>
								<?endif;?>
								<?=$arQuantityData["HTML"];?>
								<div class="cost prices clearfix">
									<?if( $arItem["OFFERS"]){?>
										<?$minPrice = false;
										if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE'])){
											// $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
											$minPrice = $arItem['MIN_PRICE'];
										}
										$offer_id=0;
										if($arParams["TYPE_SKU"]=="N"){
											$offer_id=$minPrice["MIN_ITEM_ID"];
										}
										$min_price_id=$minPrice["MIN_PRICE_ID"];
										if(!$min_price_id)
											$min_price_id=$minPrice["PRICE_ID"];
										if($minPrice["MIN_ITEM_ID"])
											$item_id=$minPrice["MIN_ITEM_ID"];
										$prefix = '';
										if('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] !== 'block' || empty($arItem['OFFERS_PROP'])){
											$prefix = GetMessage("CATALOG_FROM");
										}
										if($arParams["SHOW_OLD_PRICE"]=="Y"){?>

<div class="price" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>">
<?if(!$arItem["ISHIDEDISCOUNT"]){?>
<div class="price__old">
<?=$minPrice["PRINT_VALUE"];?>
</div>
<?}?>
<?if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
  <?=$prefix;?> <?=$minPrice["PRINT_DISCOUNT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?><span class="price_measure">/<?=$strMeasure?></span><?endif;?>
<?endif;?>
</div>
<?if(!$arItem["ISHIDEDISCOUNT"]){?>
<div class="price discount" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE_OLD']; ?>" <?=(!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '')?>>
<div>Выгода</div>
<? print_r($minPrice["PRINT_DISCOUNT_DIFF"]);?>
</div>
<? } ?>
                                          
											<?/*if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
												<div class="sale_block" <?=(!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '')?>>
													<?$percent=round(($minPrice["DISCOUNT_DIFF"]/$minPrice["VALUE"])*100, 2);?>
													<div class="value">-<?=$percent;?>%</div>
													<div class="text"><?=GetMessage("CATALOG_ECONOMY");?> <span><?=$minPrice["PRINT_DISCOUNT_DIFF"];?></span></div>
													<div class="clearfix"></div>
												</div>
											<?}*/?>
										<?}else{?>
											<div class="price only_price" id="<?=$arItemIDs["ALL_ITEM_IDS"]['PRICE']?>">
												<?if(strlen($minPrice["PRINT_DISCOUNT_VALUE"])):?>
													<?=$prefix;?> <?=$minPrice['PRINT_DISCOUNT_VALUE'];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?><span class="price_measure">/<?=$strMeasure?></span><?endif;?>
												<?endif;?>
											</div>
										<?}?>
									<?}elseif ( $arItem["PRICES"] ){?>
										<? $arCountPricesCanAccess = 0;
										$min_price_id=0;
										foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} } ?>
										<?foreach($arItem["PRICES"] as $key => $arPrice){?>
											<?if($arPrice["CAN_ACCESS"]){
												$percent=0;
												if($arPrice["MIN_PRICE"]=="Y"){
													$min_price_id=$arPrice["PRICE_ID"];
												}?>
												<?$price = CPrice::GetByID($arPrice["ID"]);?>
												<?if($arCountPricesCanAccess > 1 && false):?>
													<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
												<?endif;?>
												<?if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] && $arParams["SHOW_OLD_PRICE"]=="Y"){?>

<div class="price">
<?if(!$arItem["ISHIDEDISCOUNT"]){?>
<div class="price__old">
<?=$arPrice["PRINT_VALUE"];?>
</div>
<?}?>
<?if(strlen($arPrice["PRINT_VALUE"])):?>
<?=$arPrice["PRINT_DISCOUNT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?><span class="price_measure">/<?=$strMeasure?></span><?endif;?>
<?endif;?>
</div>
<?if(!$arItem["ISHIDEDISCOUNT"]){?>                                                    
<div class="price discount">
<div>Выгода</div>
<? print_r($arPrice["PRINT_DISCOUNT_DIFF"]);?>
</div>
<?}?>
                                              
													<?/*if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
														<div class="sale_block">
															<?$percent=round(($arPrice["DISCOUNT_DIFF"]/$arPrice["VALUE"])*100, 2);?>
															<?if($percent && $percent<100){?>
																<div class="value">-<?=$percent;?>%</div>
															<?}?>
															<div class="text"><?=GetMessage("CATALOG_ECONOMY");?> <span><?=$arPrice["PRINT_DISCOUNT_DIFF"];?></span></div>
															<div class="clearfix"></div>
														</div>
													<?}*/?>
												<?}else{?>
													<div class="price only_price">
														<?if(strlen($arPrice["PRINT_VALUE"])):?>
															<?=$arPrice["PRINT_VALUE"];?><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?><span class="price_measure">/<?=$strMeasure?></span><?endif;?>
														<?endif;?>
													</div>
												<?}?>
											<?break;}?>
										<?}?>
									<?}?>
								</div>
								<?if($arParams["SHOW_DISCOUNT_TIME"]=="Y"){?>
									<?$arDiscounts = CCatalogDiscount::GetDiscountByProduct( $arItem["ID"], $USER->GetUserGroupArray(), "N", $min_price_id, SITE_ID );
                                    $arDiscountsMy=array();
                                    foreach ($arDiscounts as $k=>$v)
                                    {
                                        $arDiscountsMy[$v["PRIORITY"]]=$v;
                                    }
                                    krsort($arDiscountsMy);                        
                                    $arDiscount=array();
                                    if($arDiscountsMy)
                                        $arDiscount=current($arDiscountsMy);
									if($arDiscount["ACTIVE_TO"]){?>
										<div class="view_sale_block">
											<div class="count_d_block">
												<span class="active_to hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
												<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
												<span class="countdown values"></span>
											</div>
<?if(false){?>                                            
											<div class="quantity_block">
												<div class="title"><?=GetMessage("TITLE_QUANTITY_BLOCK");?></div>
												<div class="values">
													<span class="item">
														<span class="value" <?=((count( $arItem["OFFERS"] ) > 0 && $arParams["TYPE_SKU"] == 'TYPE_1') ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
														<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
													</span>
												</div>
											</div>
<?}?>                                             
										</div>
									<?}?>
								<?}?>
							</div>
							<div class="buttons_block">
								<?if($arItem["OFFERS"]){?>
										<?if(!empty($arItem['OFFERS_PROP'])){?>
											<div class="sku_props">
												<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
													<?$arSkuTemplate = array();?>
													<?$arSkuTemplate=COptimus::GetSKUPropsArray($arItem['OFFERS_PROPS_JS'], $arResult["SKU_IBLOCK_ID"], $arParams["DISPLAY_TYPE"], $arParams["OFFER_HIDE_NAME_PROPS"]);?>
													<?foreach ($arSkuTemplate as $code => $strTemplate){
														if (!isset($arItem['OFFERS_PROP'][$code]))
															continue;
														echo '<div>', str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate), '</div>';
													}?>
												</div>
												<?$arItemJSParams=COptimus::GetSKUJSParams($arResult, $arParams, $arItem);?>

												<script type="text/javascript">
													var <? echo $arItemIDs["strObName"]; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
												</script>
											</div>
										<?}?>
									<?}?>
								<?if(!$arItem["OFFERS"] || ($arItem["OFFERS"] && !$arItem['OFFERS_PROP'])):?>
									<div class="counter_wrapp <?=($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '')?>">
										<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arItem["CAN_BUY"]):?>
											<div class="counter_block" data-offers="<?=($arItem["OFFERS"] ? "Y" : "N");?>" data-item="<?=$arItem["ID"];?>">
												<span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
												<input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
												<span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
											</div>
										<?endif;?>
										<div id="<?=$arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER"/*&& !$arItem["CAN_BUY"]*/)  || !$arItem["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : "");?>">
											<!--noindex-->
												<?=$arAddToBasketData["HTML"]?>
											<!--/noindex-->
										</div>
									</div>
								<?elseif($arItem["OFFERS"]):?>
									<?if(empty($arItem['OFFERS_PROP'])){?>
										<div class="offer_buy_block buys_wrapp woffers">
											<?
											$arItem["OFFERS_MORE"] = "Y";
											$arAddToBasketData = COptimus::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small read_more1', $arParams);?>
											<!--noindex-->
												<?=$arAddToBasketData["HTML"]?>
											<!--/noindex-->
										</div>
									<?}else{?>
										<div class="offer_buy_block buys_wrapp woffers" style="display:none;">
											<div class="counter_wrapp"></div>
										</div>
									<?}?>
								<?endif;?>
							</div>
						</li>
					<?}?>


<script>
	$(document).ready(function(){
		$('.catalog_block .catalog_item_wrapp .catalog_item .item-title').sliceHeight();
		$('.catalog_block .catalog_item_wrapp .catalog_item .cost').sliceHeight();
		$('.catalog_block .catalog_item_wrapp .item_info').sliceHeight({classNull: '.footer_button'});
		$('.catalog_block .catalog_item_wrapp').sliceHeight({classNull: '.footer_button'});
	});

	BX.message({
		QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.optimus", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
		QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.optimus", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
		ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
		ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
	})
</script>
<?}?>