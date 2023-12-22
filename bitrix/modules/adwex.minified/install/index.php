<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class adwex_minified extends CModule {
	const solutionName	= 'minified';
	const partnerName = 'adwex';
	const moduleClass = 'adwex_minified';

	var $MODULE_ID = 'adwex.minified';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function adwex_minified(){
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path . '/version.php');

		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = GetMessage('ADW_MINIF_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ADW_MINIF_INSTALL_DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('ADW_MINIF_PARTNER');
		$this->PARTNER_URI = GetMessage('ADW_MINIF_PARTNER_URI');
	}
	
	function InstallDB($arParams = array()) {
		
	}

	
	function UnInstallDB($arParams = array()) {
		
	}

	function DoInstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->installFiles();
        $this->InstallDB();
        \COption::SetOptionString('main', 'optimize_css_files', 'Y');
        \COption::SetOptionString('main', 'optimize_js_files', 'Y');
        RegisterModule($this->MODULE_ID);
        RegisterModuleDependences('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnBuildGlobalMenu');
        RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnEndBufferContent');
        RegisterModuleDependences('main', 'OnFileSave', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnFileSave');
        RegisterModuleDependences('main', 'OnAfterResizeImage', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterResizeImage');
        RegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockElementAdd');
        RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockElementUpdate');
        RegisterModuleDependences('iblock', 'OnAfterIBlockSectionAdd', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockSectionAdd');
        RegisterModuleDependences('iblock', 'OnAfterIBlockSectionUpdate', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockSectionUpdate');
        $APPLICATION->IncludeAdminFile(GetMessage('ADW_MINIF_INSTALL_TITLE') . ' ' . 'adwex.minified', $DOCUMENT_ROOT.'/bitrix/modules/adwex.minified/install/step.php');        
    }
     
    function DoUninstall() {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->deleteFiles();
        $this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
        UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnBuildGlobalMenu');
        UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnEndBufferContent');
        UnRegisterModuleDependences('main', 'OnFileSave', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnFileSave');
        UnRegisterModuleDependences('main', 'OnAfterResizeImage', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterResizeImage');
        UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockElementAdd');
        UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockElementUpdate');
        UnRegisterModuleDependences('iblock', 'OnAfterIBlockSectionAdd', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockSectionAdd');
        UnRegisterModuleDependences('iblock', 'OnAfterIBlockSectionUpdate', $this->MODULE_ID, 'AdwMinified\\EventListener', 'OnAfterIBlockSectionUpdate');
        $APPLICATION->IncludeAdminFile(GetMessage('ADW_MINIF_UNINSTALL_TITLE') . ' ' . 'adwex.minified', $DOCUMENT_ROOT.'/bitrix/modules/adwex.minified/install/unstep.php');
    }
    
    public function installFiles() {
        copy($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/admin/adwex_minify_image.php', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/adwex_minify_image.php');
        return true;
    }

    public function deleteFiles() {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/adwex_minify_image.php');
        return true;
    }
}