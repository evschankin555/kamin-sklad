<?
IncludeModuleLangFile(__FILE__);
class MillcomCssCompression {
	public static function OnEndBufferContent(&$content)
	{
		global $APPLICATION;
		$arCss = array_merge($APPLICATION->GetCSSArray(), $APPLICATION->arHeadAdditionalCSS);
		$arCss[] = SITE_TEMPLATE_PATH.'/template_styles.css';
		$arCss[] = SITE_TEMPLATE_PATH.'/styles.css';
		$arCss = array_unique($arCss);

		foreach ($arCss as $file) {
			$pathinfo = pathinfo($file);
			$fileMin = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.min.'.$pathinfo['extension'];
      
			$filePath = $_SERVER['DOCUMENT_ROOT'].$file;
			$fileMinPath = $_SERVER['DOCUMENT_ROOT'].$fileMin;
			if (SITE_TEMPLATE_PATH == substr($fileMin, 0, strlen(SITE_TEMPLATE_PATH))) { 
				if (file_exists($filePath) && (!file_exists($fileMinPath) || filemtime($fileMinPath) < filemtime($filePath))) {
					$fileContent = file_get_contents($filePath);
					$fileContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $fileContent);
					$fileContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $fileContent);
					$fileContent = str_replace(array(' 0px', ': ', '; ', ' { ', ' } ', '{ ', ' {', ' }', '} ', ', ', ';}'), array(' 0', ':', ';', '{', '}', '{', '{', '}', '}', ',', '}'), $fileContent);
					if (!$handle = fopen($fileMinPath, 'w')) {
						self::toLog(GetMessage("CSS_COMPRESS_ERROR"), $fileMinPath, GetMessage("CSS_COMPRESS_ERROR_OPEN"));
					} else {
						if (fwrite($handle, $fileContent) === FALSE) {
							self::toLog(GetMessage("CSS_COMPRESS_ERROR"), $fileMinPath, GetMessage("CSS_COMPRESS_ERROR_WRITE"));
						} else {
							self::toLog(GetMessage("CSS_COMPRESS_OK"), $fileMinPath, false); 
						}
						fclose($handle); 
					}
				}
			}
		}
	}
	public static function toLog($AUDIT_TYPE_ID, $ITEM_ID, $DESCRIPTION) {
		CEventLog::Add(array(
			"SEVERITY" => "SECURITY",
			"AUDIT_TYPE_ID" => $AUDIT_TYPE_ID,
			"MODULE_ID" => "millcom.csscompression",
			"ITEM_ID" => $ITEM_ID,
			"SITE_ID" => SITE_ID,
			"DESCRIPTION" => $DESCRIPTION
		));
	}
}
?>