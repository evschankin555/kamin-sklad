<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Купить камин в #WF_CITY_ROD#. Интернет-магазин");
$APPLICATION->SetPageProperty("keywords", "камины, купить камин, камины для дачи, камины для дома, продажа каминов, интернет-магазин каминов, kamin-sklad.ru, #WF_CITY_NAME#");
$APPLICATION->SetPageProperty("description", "Интернет-магазин каминов Kamin-Sklad.ru: продаем камины и печи 20 лет, доставка в #WF_CITY_NAME#");
$APPLICATION->SetPageProperty("viewed_show", "Y");
$APPLICATION->SetTitle("Главная");
/*
$dir = $APPLICATION->GetCurUri();
$arrUri = explode( '&', $dir );
$dir = $arrUri[0];

switch( $dir ) {
  case '/catalog/pechi/vermont-castings/encore':
    LocalRedirect( '/catalog/pechi/vermont-castings/encore/', false, '301 Moved permanently' );
    break;
  case '/catalog/topki/topki-totem/':
    LocalRedirect( '/catalog/topki/bolshie-topki-totem/', false, '301 Moved permanently' );
    break;
  case '/catalog/topki/piazzetta/':
    LocalRedirect( '/catalog/topki/topki-so-steklom-piazzetta/', false, '301 Moved permanently' );
    break;
  case '/catalog/electric-fireplaces/':
    LocalRedirect( '/', false, '301 Moved permanently' );
    break;
}
*/
if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']!="Y") {
?><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/comp_banners_top_slider.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/comp_tizers.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<div class="main-page-banners-wrapper">
	<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"PATH" => SITE_DIR."include/mainpage/comp_banners_float.php",
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "",
			"AREA_FILE_RECURSIVE" => "Y",
			"EDIT_TEMPLATE" => "standard.php"
		),
		false
	);?>
</div>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/comp_catalog_hit.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/comp_news_akc.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/inc_company.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>	

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/gallery.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/comp_brands.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<? } else { ?>

<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PATH" => SITE_DIR."include/mainpage/pageCity.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php"
	),
	false
);?>

<? } ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>