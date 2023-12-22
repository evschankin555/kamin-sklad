<?

IncludeModuleLangFile(__FILE__);

class millcom_csscompression extends CModule {
	var $MODULE_ID = "millcom.csscompression";		
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;	
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	
	function millcom_csscompression() {
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = GetMessage("CSS_COMPRESS_PARTNER_NAME");
		$this->PARTNER_URI = "http://millcom.by";


		$this->MODULE_NAME = GetMessage("CSS_COMPRESS_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("CSS_COMPRESS_MODULE_DESCRIPTION");	
	}

	function DoInstall() {
		RegisterModule($this->MODULE_ID);
		RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'MillcomCssCompression', 'OnEndBufferContent');
		return true;
	}
	
	function DoUninstall() {		
		UnRegisterModule($this->MODULE_ID); 
		UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'MillcomCssCompression', 'OnEndBufferContent');
		return true;
	}
}
?>