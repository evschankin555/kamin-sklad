<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") die();?>
<?IncludeTemplateLangFile(__FILE__);?>

							<?if(!COptimus::IsMainPage()):?>
								</div> <?// .container?>
							<?endif;?>
						</div>                        
					<?if(!COptimus::IsOrderPage() && !COptimus::IsBasketPage()):?>
						</div> <?// .right_block?>
					<?endif;?>                                              
				</div> <?// .wrapper_inner?>				
			</div> <?// #content?>
		</div><?// .wrapper?>
		<footer id="footer">
			<div class="footer_inner <?=strtolower($TEMPLATE_OPTIONS["BGCOLOR_THEME_FOOTER_SIDE"]["CURRENT_VALUE"]);?>">
<? if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y") {
$detect = new \Bitrix\Conversion\Internals\MobileDetect;
?>
				<?if(($APPLICATION->GetProperty("viewed_show")=="Y" || defined("ERROR_404"))):// && !$detect->isMobile()):?>
					<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"PATH" => SITE_DIR."include/footer/comp_viewed.php",
							"AREA_FILE_SHOW" => "file",
							"AREA_FILE_SUFFIX" => "",
							"AREA_FILE_RECURSIVE" => "Y",
                            "CACHE_TYPE" => "Y",
                            "CACHE_TIME" => "36000000",
                            "CACHE_FILTER" => "Y",
                            "CACHE_GROUPS" => "N",
							"EDIT_TEMPLATE" => "standard.php"
						),
						false
					);?>					
				<?endif;?>
<? } ?>                
				<div class="wrapper_inner">
					<div class="footer_bottom_inner">
						<div class="left_block">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/footer/copyright.php",
									"AREA_FILE_SHOW" => "file",
									"AREA_FILE_SUFFIX" => "",
									"AREA_FILE_RECURSIVE" => "Y",
                                    "CACHE_TYPE" => "Y",
                                    "CACHE_TIME" => "36000000",
                                    "CACHE_FILTER" => "Y",
                                    "CACHE_GROUPS" => "N",
									"EDIT_TEMPLATE" => "standard.php"
								),
								false
							);?>							
							<div id="bx-composite-banner"></div>
						</div>
						<div class="right_block">
							<div class="middle">
								<div class="rows_block">
									<div class="item_block col-75 menus">
<? if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y" && false) { ?>
										<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_submenu_top", array(
											"ROOT_MENU_TYPE" => "bottom",
											"MENU_CACHE_TYPE" => "Y",
											"MENU_CACHE_TIME" => "36000000",
											"MENU_CACHE_USE_GROUPS" => "N",
											"MENU_CACHE_GET_VARS" => array(),
											"MAX_LEVEL" => "1",
											"USE_EXT" => "N",
											"DELAY" => "N",
											"ALLOW_MULTI_SELECT" => "N"
											),false
										);?>
<? } ?>                                        
										<div class="rows_block">
											<div class="item_block col-4">
<? if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y") { ?>                                            
												<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_submenu", array(
													"ROOT_MENU_TYPE" => "bottom_company",
													"MENU_CACHE_TYPE" => "Y",
													"MENU_CACHE_TIME" => "36000000",
													"MENU_CACHE_USE_GROUPS" => "N",
													"MENU_CACHE_GET_VARS" => array(),
													"MAX_LEVEL" => "1",
													"USE_EXT" => "N",
													"DELAY" => "N",
													"ALLOW_MULTI_SELECT" => "N"
													),false
												);?>
<? } ?>                                                
											</div>
											<div class="item_block col-4">
<? if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y") { ?>                                            
												<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_submenu", array(
													"ROOT_MENU_TYPE" => "bottom_info",
													"MENU_CACHE_TYPE" => "Y",
													"MENU_CACHE_TIME" => "36000000",
													"MENU_CACHE_USE_GROUPS" => "N",
													"MENU_CACHE_GET_VARS" => array(),
													"MAX_LEVEL" => "1",
													"USE_EXT" => "N",
													"DELAY" => "N",
													"ALLOW_MULTI_SELECT" => "N"
													),false
												);?>
<? } ?>                                                
											</div>
											<div class="item_block col-4">
<? if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y") { ?>                                            
												<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom_submenu", array(
													"ROOT_MENU_TYPE" => "bottom_help",
													"MENU_CACHE_TYPE" => "Y",
													"MENU_CACHE_TIME" => "36000000",
													"MENU_CACHE_USE_GROUPS" => "N",
													"MENU_CACHE_GET_VARS" => array(),
													"MAX_LEVEL" => "1",
													"USE_EXT" => "N",
													"DELAY" => "N",
													"ALLOW_MULTI_SELECT" => "N"
													),false
												);?>
<? } ?>                                                
											</div>
											<div class="item_block col-4">
												<div class="foot__adres">
                                                    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
                                                        array(
                                                            "COMPONENT_TEMPLATE" => ".default",
                                                            "PATH" => SITE_DIR."include/footer/copy/address.php",
                                                            "AREA_FILE_SHOW" => "file",
                                                            "AREA_FILE_SUFFIX" => "",
                                                            "AREA_FILE_RECURSIVE" => "Y",
                                                            "CACHE_TYPE" => "Y",
                                                            "CACHE_TIME" => "36000000",
                                                            "CACHE_FILTER" => "Y",
                                                            "CACHE_GROUPS" => "N",
                                                            "EDIT_TEMPLATE" => "standard.php"
                                                        ),
                                                        false
                                                    );?>
												</div>
											</div>                                            
										</div>
									</div>
									<div class="item_block col-4 soc">
										<div class="soc_wrapper">
											<div class="phones">
												<div class="phone_block">
													<div class="free">бесплатно по России</div>
													<span class="phone_wrap">
														<span class="icons fa fa-phone"></span>
														<span>
															<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
																array(
																	"COMPONENT_TEMPLATE" => ".default",
																	"PATH" => SITE_DIR."include/phone.php",
																	"AREA_FILE_SHOW" => "file",
																	"AREA_FILE_SUFFIX" => "",
																	"AREA_FILE_RECURSIVE" => "Y",
                                                                    "CACHE_TYPE" => "Y",
                                                                    "CACHE_TIME" => "36000000",
                                                                    "CACHE_FILTER" => "Y",
                                                                    "CACHE_GROUPS" => "N",
																	"EDIT_TEMPLATE" => "standard.php"
																),
																false
															);?>
														</span>
													</span>
													<span class="order_wrap_btn">
														<span class="callback_btn"><?=GetMessage('CALLBACK')?></span>
													</span>
												</div>
											</div>
											<div class="social_wrapper">
												<div class="social">
													<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
														array(
															"COMPONENT_TEMPLATE" => ".default",
															"PATH" => SITE_DIR."include/footer/social.info.optimus.default.php",
															"AREA_FILE_SHOW" => "file",
															"AREA_FILE_SUFFIX" => "",
															"AREA_FILE_RECURSIVE" => "Y",
                                                            "CACHE_TYPE" => "Y",
                                                            "CACHE_TIME" => "36000000",
                                                            "CACHE_FILTER" => "Y",
                                                            "CACHE_GROUPS" => "N",
															"EDIT_TEMPLATE" => "standard.php"
														),
														false
													);?>
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="foot__txt">Предложение не является публичной офертой. Для получения подробной информации о стоимости, модификациях, сроках и условиях поставки просьба обращаться по указанным телефонам.</div>
					</div>
					<div class="mobile_copy">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
							array(
								"COMPONENT_TEMPLATE" => ".default",
								"PATH" => SITE_DIR."include/footer/copyright.php",
								"AREA_FILE_SHOW" => "file",
								"AREA_FILE_SUFFIX" => "",
								"AREA_FILE_RECURSIVE" => "Y",
                                "CACHE_TYPE" => "Y",
                                "CACHE_TIME" => "36000000",
                                "CACHE_FILTER" => "Y",
                                "CACHE_GROUPS" => "N",
								"EDIT_TEMPLATE" => "standard.php"
							),
							false
						);?>
					</div>
					<?//$APPLICATION->IncludeFile(SITE_DIR."include/bottom_include1.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("ARBITRARY_1"))); ?>
					<?//$APPLICATION->IncludeFile(SITE_DIR."include/bottom_include2.php", Array(), Array("MODE" => "text", "NAME" => GetMessage("ARBITRARY_2"))); ?>
				</div>
			</div>
		</footer>

<!-- ВИДЖЕТ ОБРАТНОГО ЗВОНКА:{literal}-->

<link rel="stylesheet" href="https://cdn.envybox.io/widget/cbk.css" async>
<script type="text/javascript" src="https://cdn.envybox.io/widget/cbk.js?wcb_code=37a237c0b75b9b1ae956ef89ee7a9cac" charset="UTF-8" async></script>
<!-----------------------------{/literal}-->


<?
COptimus::setFooterTitle();
COptimus::showFooterBasket();

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/fancybox/helpers/jquery.fancybox-buttons.css', true);
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/fancybox/helpers/jquery.fancybox-thumbs.css', true);
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/fancybox/jquery.fancybox.pack.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/fancybox/helpers/jquery.fancybox-buttons.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/fancybox/helpers/jquery.fancybox-media.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/fancybox/helpers/jquery.fancybox-thumbs.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/velocity.min.js');


//print_r($_SERVER['REMOTE_ADDR']);
print_r("<!--<pre>");
//print_r($_SESSION['CURRENT_LOCATION']['CURRENT']);
//$tit = $APPLICATION->GetPageProperty("title") . ' в ' .$_SESSION['CURRENT_LOCATION']['CURRENT']['NAMESKLON'];
print_r("</pre>-->");
//$APPLICATION->SetPageProperty('title', $tit);
?>

<div style="display:none">
#WF_COUNT#
</div>
<?$APPLICATION->IncludeComponent( "abricos:antisovetnik", "", array(), false);?>
<?$APPLICATION->IncludeComponent(
    "webfly:meta.edit",
    ".default",
    array(
        "CACHE_TYPE" => "Y",
        "CACHE_TIME" => "3600",
        "WF_JQUERY" => "N"
    ),
    false
);?>

	</body>
<!-- mch -->
</html>
