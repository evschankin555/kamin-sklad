<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

use \Bitrix\Main\Context;

class twim_recaptchafree extends CModule
{
    public $MODULE_ID = "twim.recaptchafree";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $MODULE_CSS;
	public $MODULE_GROUP_RIGHTS = "Y";
    public $server;
    public $doc_root;
    

 
	function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("CAPTCHA_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("CAPTCHA_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
        $this->server = Context::getCurrent()->getServer();
        $this->doc_root = $this->server->getDocumentRoot();
	}

  function InstallDB()
    {
        RegisterModule("twim.recaptchafree");
        return true;
    }

    function UnInstallDB()
    {	
		COption::RemoveOption("twim.recaptchafree");
        UnRegisterModule("twim.recaptchafree");
        return true;
    }

    function InstallEvents()
    {
		RegisterModuleDependences("main", "OnPageStart", "twim.recaptchafree", "ReCaptchaTwoGoogle", "OnVerificContent");
		RegisterModuleDependences("main", "OnEndBufferContent", "twim.recaptchafree", "ReCaptchaTwoGoogle", "OnAddContentCaptcha");
        return true;
    }

    function UnInstallEvents()
    {	
		UnRegisterModuleDependences("main", "OnPageStart", "twim.recaptchafree", "ReCaptchaTwoGoogle", "OnVerificContent");
		UnRegisterModuleDependences("main", "OnEndBufferContent", "twim.recaptchafree", "ReCaptchaTwoGoogle", "OnAddContentCaptcha");
        return true;
    }

    function InstallFiles()
    {   
		CopyDirFiles($this->doc_root."/bitrix/modules/twim.recaptchafree/install/js", $this->doc_root."/bitrix/js", true, true);
        $arLang = ["ru", "en"];
        foreach ($arLang as $langCode) {
            $pathLangSite = $this->doc_root . "/bitrix/php_interface/user_lang/".$langCode."/lang.php";
            $pathLangModule = $this->doc_root . "/bitrix/modules/twim.recaptchafree/public/user_lang/".$langCode."/lang.php";
            if (file_exists($pathLangSite)) {
				$fileLangSite = file_get_contents($pathLangSite);
				if(strpos($fileLangSite, "###TWIM.RECAPTCHAFREE###") === false){
					$fileLang = file_get_contents($pathLangModule);
					file_put_contents($pathLangSite, $fileLang, FILE_APPEND);
				}
            } else {
                CheckDirPath($this->doc_root . "/bitrix/php_interface/user_lang/".$langCode."/");
                CopyDirFiles($pathLangModule, $pathLangSite, true, true);
            }
        }        
        return true;
    }

    function UnInstallFiles()
    {
		DeleteDirFilesEx("/bitrix/js/twim.recaptchafree/");
        $arLang = ["ru", "en"];
        foreach ($arLang as $langCode) {
            $pathLangSite = $this->doc_root . "/bitrix/php_interface/user_lang/".$langCode."/lang.php";
            if (file_exists($pathLangSite)) {
                $lang_content = file_get_contents($this->doc_root . "/bitrix/php_interface/user_lang/".$langCode."/lang.php");
                $lang_content_rep = preg_replace('/###TWIM.RECAPTCHAFREE###.+?###\/\/\/TWIM.RECAPTCHAFREE###/is', '', $lang_content);
                RewriteFile($pathLangSite, $lang_content_rep);
            }
        }
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;

        if (!IsModuleInstalled("twim.recaptchafree"))
        {
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
            $APPLICATION->IncludeAdminFile(GetMessage("CAPTCHA_INSTALL_TITLE"), __DIR__."/step.php");
            
        }
    }

    function DoUninstall()
    {
        global $APPLICATION;
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        //$APPLICATION->IncludeAdminFile(GetMessage("CAPTCHA_UNINSTALL_TITLE"), __DIR__."/unstep.php");
    }
}
?>