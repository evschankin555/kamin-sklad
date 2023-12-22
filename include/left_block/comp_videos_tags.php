<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:search.tags.cloud", 
	"news_left", 
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COLOR_NEW" => "3E74E6",
		"COLOR_OLD" => $_GET["arrFilter_ff"]["TAGS"],
		"COLOR_TYPE" => "Y",
		"FILTER_NAME" => "arrBlog",
		"FONT_MAX" => "50",
		"FONT_MIN" => "10",
		"PAGE_ELEMENTS" => "10",
		"PERIOD" => "",
		"PERIOD_NEW_TAGS" => "",
		"SHOW_CHAIN" => "Y",
		"SORT" => "NAME",
		"TAGS_INHERIT" => "Y",
		"URL_SEARCH" => "",
		"WIDTH" => "100%",
		"arrFILTER" => array(
			0 => "iblock_aspro_optimus_content",
		),
		"arrFILTER_blog" => array(
			0 => "all",
		),
		"arrFILTER_iblock_content" => array(
			0 => "19",
		),
		"COMPONENT_TEMPLATE" => "videos",
		"arrFILTER_iblock_aspro_optimus_content" => array(
			0 => "19",
		)
	),
	false
);?>