<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage('COMPONENT_NAME'),
	"DESCRIPTION" => GetMessage('COMPONENT_DESCRIPTION'),
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "webfly", // for example "my_project"
		"CHILD" => array(
			"ID" => "webfly_seo", // for example "my_project:services"
			"NAME" => "metatags",  // for example "Services"
		),
	),
	"COMPLEX" => "N",
);

?>