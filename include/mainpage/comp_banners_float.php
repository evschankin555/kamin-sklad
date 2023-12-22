<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?global $SITE_THEME, $TEMPLATE_OPTIONS;?>
<div class="wrapper_inner1 wides float_banners">
	<?$APPLICATION->IncludeComponent("aspro:com.banners.optimus", "optimus-main-page", Array(
	"IBLOCK_TYPE" => "aspro_optimus_adv",	// Тип информационного блока (используется только для проверки)
		"IBLOCK_ID" => "2",	// Код информационного баннеров
		"TYPE_BANNERS_IBLOCK_ID" => "1",	// Код информационного блока типов баннеров
		"SET_BANNER_TYPE_FROM_THEME" => "N",	// Устанавливать тип баннера в соответствие с цветовой схемой
		"NEWS_COUNT" => "9",	// Количество баннеров на странице
		"SORT_BY1" => "SORT",	// Поле для первой сортировки баннеров
		"SORT_ORDER1" => "ASC",	// Направление для первой сортировки баннеров
		"SORT_BY2" => "ID",	// Поле для второй сортировки баннеров
		"SORT_ORDER2" => "DESC",	// Направление для второй сортировки баннеров
		"PROPERTY_CODE" => array(	// Свойства
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
		"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
		"SITE_THEME" => $SITE_THEME,
		"BANNER_TYPE_THEME" => "FLOAT",
		"COMPONENT_TEMPLATE" => "optimus",
		"CATALOG" => "/catalog/",	// Путь до каталога
	),
	false
);?>
</div>
<div class="clearfix"></div>
