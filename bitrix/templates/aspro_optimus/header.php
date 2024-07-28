<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if($GET["debug"] == "y"){
	error_reporting(E_ERROR | E_PARSE);
}
IncludeTemplateLangFile(__FILE__);
global $APPLICATION, $TEMPLATE_OPTIONS, $arSite;
$arSite = CSite::GetByID(SITE_ID)->Fetch();
$htmlClass = ($_REQUEST && isset($_REQUEST['print']) ? 'print' : false);
CModule::IncludeModule("iblock");
require_once("include/func.php");
?>
<!DOCTYPE html>
<html xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>" xmlns="http://www.w3.org/1999/xhtml" <?=($htmlClass ? 'class="'.$htmlClass.'"' : '')?> itemscope itemtype="https://schema.org/WebSite">
<head>

<?
require_once("include_area/location-session.php");
?>
	<title><?$APPLICATION->ShowTitle()?></title>
	<?$APPLICATION->ShowMeta("viewport");?>
	<?$APPLICATION->ShowMeta("HandheldFriendly");?>
	<?$APPLICATION->ShowMeta("apple-mobile-web-app-capable", "yes");?>
	<?$APPLICATION->ShowMeta("apple-mobile-web-app-status-bar-style");?>
	<?$APPLICATION->ShowMeta("SKYPE_TOOLBAR");?>
	<?$APPLICATION->ShowHead();?>
	<?$APPLICATION->AddHeadString('<script>BX.message('.CUtil::PhpToJSObject( $MESS, false ).')</script>', true);?>
	<?if(CModule::IncludeModule("aspro.optimus")) {COptimus::Start(SITE_ID);}?>
	<!--[if gte IE 9]><style type="text/css">.basket_button, .button30, .icon {filter: none;}</style><![endif]-->
    <link preload rel="stylesheet" href="<?=CMain::IsHTTPS() ? 'https' : 'http'?>://fonts.googleapis.com/css?family=Ubuntu:400,500,700,400italic&subset=latin,cyrillic&display=swap" type="text/css">
<?

$current_region_id = $_SESSION['CURRENT_LOCATION']['CURRENT']['ID'];
  ?>

<meta name="cmsmagazine" content="8b8f98ba6e95d71cf42e9faa2011c4a5" />
<meta name="robots" content="noyaca"/>


</head>
	<body id="main">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MHGFWH2"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
		<div id="panel"><?$APPLICATION->ShowPanel();?></div>
		<?if(!CModule::IncludeModule("aspro.optimus")){?><center><?$APPLICATION->IncludeFile(SITE_DIR."include/error_include_module.php");?></center></body></html><?die();?><?}?>
		<?$APPLICATION->IncludeComponent("aspro:theme.optimus", ".default", array("COMPONENT_TEMPLATE" => ".default"), false);?>
		<?COptimus::SetJSOptions();?>
		<div class="wrapper <?=(COptimus::getCurrentPageClass());?> basket_<?=strToLower($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"]);?> <?=strToLower($TEMPLATE_OPTIONS["MENU_COLOR"]["CURRENT_VALUE"]);?> banner_auto">
			<div class="header_wrap <?=strtolower($TEMPLATE_OPTIONS["HEAD_COLOR"]["CURRENT_VALUE"])?>">
				<?if($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"]=="NORMAL"){?>
					<div class="top-h-row">
						<div class="wrapper_inner">
							<div class="top_inner">
								<div class="content_menu">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
										array(
											"COMPONENT_TEMPLATE" => ".default",
											"PATH" => SITE_DIR."include/topest_page/menu.top_content_row.php",
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
								<div class="phones">
									<div class="phone_block">
										<span class="phone_wrap">
											<span class="icons fa fa-phone"></span>
											<span class="phone_text">
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
											<?
											if($current_region_id == 3529)
											{
												//onclick="jivo_api.open({start: 'call'});"
											?>
											<a class="callback_btn_utc" href="#callbackwidget" >Заказать звонок</a>
											<?
											}
											else
											{
												//#uptocall
											?>
											<a class="callback_btn_utc" href="#callbackwidget">Заказать звонок</a>
											<?
											} 
											?>
										</span>
									</div>
								</div>
								<div class="h-user-block" id="personal_block">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
										array(
											"COMPONENT_TEMPLATE" => ".default",
											"PATH" => SITE_DIR."include/topest_page/auth.top.php",
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
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
				<?}?>
				<header id="header">
					<div class="wrapper_inner">
						<div class="top_br"></div>
						<table class="middle-h-row">
							<tr>
								<td class="logo_wrapp">
									<div class="logo nofill_<?=strtolower(\Bitrix\Main\Config\Option::get('aspro.optimus', 'NO_LOGO_BG', 'N'));?>">
<a href="/"><img alt="" title="КаминСклад" src="/logo.png"></a>
									</div>

									<div class="grd_pzf grd_pzf-mobile">

										<?
										global $USER;
										if ($USER->IsAdmin() || in_array(10, $USER->GetUserGroupArray())) {
										?>
										                                    <a href="#" class="changeCity"><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["NAME"]?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
										<? } else {
                                            /*	<span class="changeCity2"><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["NAME"]?></span>*/
                                            ?>
                                            <a href="#" class="changeCity"><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["NAME"]?>
                                                <i class="fa fa-chevron-down" aria-hidden="true"></i></a>

										<? } ?>
										</div>

								</td>
								
								<td  class="center_block">																	
									<div class="grd_pzf">Ваш город: 

<?
global $USER;
if ($USER->IsAdmin() || in_array(10, $USER->GetUserGroupArray())) {
?>
                                    <a href="#" class="changeCity"><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["NAME"]?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
<? } else {
   /* <span class="changeCity2"><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["NAME"]?></span>*/
    ?>
    <a href="#" class="changeCity"><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["NAME"]?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>

<? } ?>
</div>
<div class="search">
										<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
											array(
												"COMPONENT_TEMPLATE" => ".default",
												"PATH" => SITE_DIR."include/top_page/search.title.catalog.php",
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

								</td>
								<td class="text_wrapp">
									<div class="slogan">
<?
global $USER;
if ($USER->IsAdmin()) {
?>                                    

<? } else { ?>

<? } ?>
										<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
											array(
												"COMPONENT_TEMPLATE" => ".default",
												"PATH" => SITE_DIR."include/top_page/slogan.php",
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
									<!-- <a href="/registratsiya-spetsialista/" class="cupon">Купон монтажника</a> -->
								</td>
								<td class="basket_wrapp">
									<?if($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"] == "NORMAL"){?>
										<div class="wrapp_all_icons">
											<div class="header-compare-block icon_block iblock" id="compare_line" >
												<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
													array(
														"COMPONENT_TEMPLATE" => ".default",
														"PATH" => SITE_DIR."include/top_page/catalog.compare.list.compare_top.php",
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
											<div class="header-cart" id="basket_line">
												<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
													array(
														"COMPONENT_TEMPLATE" => ".default",
														"PATH" => SITE_DIR."include/top_page/comp_basket_top.php",
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
									<?}else{?>
										<div class="header-cart fly" id="basket_line">
											<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
												array(
													"COMPONENT_TEMPLATE" => ".default",
													"PATH" => SITE_DIR."include/top_page/comp_basket_top.php",
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
										<div class="middle_phone">
                                            <a href="/registratsiya-spetsialista/" class="btn_montag2">Купон монтажника</a>                                            
											<div class="phones">
												<span class="phone_wrap">
													<span class="free">бесплатно по России</span>
													<span class="phone">

														<span class="phone_text">
															<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
																array(
																	"COMPONENT_TEMPLATE" => ".default",
																	"PATH" => SITE_DIR."include/phone.php",
																	"AREA_FILE_SHOW" => "file",
																	"AREA_FILE_SUFFIX" => "",
																	"AREA_FILE_RECURSIVE" => "Y",
                                                                    "CACHE_TYPE" => "N",
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
													<?
													if($current_region_id == 3529)
													{
													?>
													<a class="callback_btn_utc" href="#" onclick="jivo_api.open({start: 'call'});">Заказать звонок</a>
													<?
													}
													else
													{
													?>
													<a class="callback_btn_utc" href="#uptocall">Заказать звонок</a>
													<?
													} 
													?>
													</span>
												</span>
											</div>

										</div>
									<?}?>
									<div class="clearfix"></div>
								</td>
							</tr>
						</table>
					</div>
<? if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y") { ?>                    
					<div class="catalog_menu menu_<?=strToLower($TEMPLATE_OPTIONS["MENU_COLOR"]["CURRENT_VALUE"]);?>">
						<div class="wrapper_inner">
							<div class="wrapper_middle_menu wrap_menu">
								<ul class="menu adaptive">
									<li class="menu_opener">
										<span class="search-menu-btn"></span>
										<div class="text">
											<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
												array(
													"COMPONENT_TEMPLATE" => ".default",
													"PATH" => SITE_DIR."include/menu/menu.mobile.title.php",
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
									</li>
								</ul>				
								<div class="catalog_menu_ext">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
										array(
											"COMPONENT_TEMPLATE" => ".default",
											"PATH" => SITE_DIR."include/menu/menu.catalog.php",
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
								<div class="inc_menu">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
										array(
											"COMPONENT_TEMPLATE" => ".default",
											"PATH" => SITE_DIR."include/menu/menu.top_content_multilevel.php",
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
<? } ?>                    
				</header>
			</div>
			<div class="wraps" id="content">
				<div class="wrapper_inner <?=(COptimus::IsMainPage() ? "front" : "");?> <?=((COptimus::IsOrderPage() || COptimus::IsBasketPage()) ? "wide_page" : "");?>">

					<?if(!COptimus::IsOrderPage() && !COptimus::IsBasketPage() && $_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y"){?>
						<div class="left_block">
<style>
    .catalog-left{
        height: 480px;
        display: block;
        margin-bottom: 15px;
    }
    header .menu_top_block li.catalog>.dropdown{
        display: block!important;
        margin-top: 15px;
    }
</style>
                            <div class="catalog-left"></div>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/menu.left_menu.php",
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

							<?$APPLICATION->ShowViewContent('left_menu');?>

							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_banners_left.php",
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
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_subscribe.php",
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
                            
							<?if (strstr($_SERVER['REQUEST_URI'], '/news/') && substr_count($APPLICATION->GetCurDir(), '/')==2):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_news_tags.php",
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
							<?endif?>                     
							<?if (strstr($_SERVER['REQUEST_URI'], '/articles/') && (substr_count($APPLICATION->GetCurDir(), '/')==2 || substr_count($APPLICATION->GetCurDir(), '/')==3)):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_articles_tags.php",
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
							<?endif?> 
							<?if (strstr($_SERVER['REQUEST_URI'], '/videos/') && substr_count($APPLICATION->GetCurDir(), '/')==3):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_videos_tags.php",
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
							<?endif?>                             
                                                        
							<?if (!strstr($_SERVER['REQUEST_URI'], '/news/')):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_news.php",
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
							<?endif?>
                           
							<?if (!strstr($_SERVER['REQUEST_URI'], '/articles/')):?>							
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/left_block/comp_news_articles.php",
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
							<?endif?>                                 
						</div>
						<div class="right_block">
					<?}?>          
						<div class="<?if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y"){?>middle<?}?>">
							<?if(!COptimus::IsMainPage()):?>
								<div class="container">
									<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "optimus", array(
										"START_FROM" => "0",
										"PATH" => "",
										"SITE_ID" => "-",
                                        "CACHE_TYPE" => "Y",
                                        "CACHE_TIME" => "36000000",
                                        "CACHE_FILTER" => "Y",
                                        "CACHE_GROUPS" => "N",
										"SHOW_SUBSECTIONS" => "N"
										),
										false
									);?>
									<h1 id="pagetitle"><?=$APPLICATION->ShowTitle(false);?></h1>								
							<?endif;?>
                                   
<?if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") $APPLICATION->RestartBuffer();?>
