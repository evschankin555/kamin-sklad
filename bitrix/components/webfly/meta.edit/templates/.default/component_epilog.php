<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(strlen($arResult['H1'])>0) $APPLICATION->SetTitle(html_entity_decode($arResult['H1']));
if(strlen($arResult['TITLE'])>0) $APPLICATION->SetPageProperty('title',html_entity_decode($arResult['TITLE']));
if(strlen($arResult['ROBOTS'])>0) $APPLICATION->SetPageProperty('robots',html_entity_decode($arResult['ROBOTS']));
if(strlen($arResult['KEYWORDS'])>0) $APPLICATION->SetPageProperty('keywords',html_entity_decode($arResult['KEYWORDS']));
if(strlen($arResult['DESCRIPTION'])>0) $APPLICATION->SetPageProperty('description',html_entity_decode($arResult['DESCRIPTION']));

if($USER->IsAdmin()) {
    Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/components/webfly/meta.edit/templates/.default/script_t.js');
    Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/components/webfly/meta.edit/templates/.default/style_t.css');
}
?>