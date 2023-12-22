<?

Class abricos_antisovetnik extends CModule
{
var $MODULE_ID = "abricos.antisovetnik";
var $MODULE_VERSION;
var $MODULE_VERSION_DATE;
var $MODULE_NAME;
var $MODULE_DESCRIPTION;
var $MODULE_CSS;

function abricos_antisovetnik()
{
$arModuleVersion = array();

$path = str_replace("\\", "/", __FILE__);
$path = substr($path, 0, strlen($path) - strlen("/index.php"));
include($path."/version.php");

if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
{
$this->MODULE_VERSION = $arModuleVersion["VERSION"];
$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
}

$this->MODULE_NAME = GetMessage("ABRICOSANTI_MODULE_NAME");
$this->MODULE_DESCRIPTION = GetMessage("ABRICOSANTI_MODULE_DESCRIPTION");
$this->PARTNER_NAME = GetMessage("ABRICOSANTI_PARTNER_NAME");
$this->PARTNER_URI = "http://sibbitrix.ru";

}

function InstallFiles($arParams = array())
{
CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/abricos.antisovetnik/install/components/abricos/antisovetnik/templates/.default/js",
             $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/abricos", true, true);
             CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/abricos.antisovetnik/install/components",
             $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
return true;
}

function UnInstallFiles()
{
DeleteDirFilesEx("/bitrix/components/abricos/antisovetnik");
return true;
}

function DoInstall()
{
global $DOCUMENT_ROOT, $APPLICATION;
$this->InstallFiles();
RegisterModule("abricos.antisovetnik");
$APPLICATION->IncludeAdminFile(GetMessage("ABRICOSANTI_MODULE_INSTALL"), $DOCUMENT_ROOT."/bitrix/modules/abricos.antisovetnik/install/step.php");
}

function DoUninstall()
{
global $DOCUMENT_ROOT, $APPLICATION;
$this->UnInstallFiles();
UnRegisterModule("abricos.antisovetnik");
$APPLICATION->IncludeAdminFile(GetMessage("ABRICOSANTI_MODULE_UNINSTALL"), $DOCUMENT_ROOT."/bitrix/modules/abricos.antisovetnik/install/unstep.php");
}
}

?>