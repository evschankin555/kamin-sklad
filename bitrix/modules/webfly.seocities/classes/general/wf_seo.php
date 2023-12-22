<?

/**
 * class works with wf_seo IB
 */
class CWebflySeo {

    static $seo = '';

    public function __construct() {
    }

    /**
     * Get full current page path
     * @return string $pagePath - full path to current page
     */
    function GetPageDir() {
        global $APPLICATION;
        $fullDomain = CSeoCities::GetDomain($_SERVER["HTTP_HOST"], ''); //with www (www.site.ru or www.msk.site.ru)
        $pagePath = trim($APPLICATION->sDirPath); //path ro page /catalog/
        $pagePath = $fullDomain . $pagePath; //full path to page www.msk.site.ru/catalog/
        return $pagePath;
    }

    /**
     * Get Seo Texts From IB for current page
     * @return array $pageSeoTexts - all seo texts for current page
     */
    function GetPageSeoTexts() {//Work OnEpilog?
        if (!strstr($_SERVER["REQUEST_URI"], '/bitrix/')) {
            global $APPLICATION;
            global $USER;
            $pagePath = self::GetPageDir(); //full path to page www.msk.site.ru/catalog/
            $obCache = new CPHPCache;
            $iLifeTime = 60 * 60 * 24 * 30;
            $pageSeoTexts = false;
            //Cash on each existing page of the webfly_seo IB
            $sCacheIDS = 'seo' . SITE_ID . $pagePath;
            if ($obCache->InitCache($iLifeTime, $sCacheIDS, SITE_ID . WF_SEOCITIES_CACHEFOLDER . WF_SEO_IBLOCK . '/')) {
                $arVars = $obCache->GetVars();
                $arFieldsSeo = $arVars['arFieldsSeo'];
            }
            elseif ($obCache->StartDataCache()) {
                if (CModule::IncludeModule('iblock')) {
                    $arSelect = Array(
                      "ID",
                      "IBLOCK_ID",
                      "NAME",
                      "PROPERTY_*",
                    );
                    $arFilter = Array("IBLOCK_CODE" => WF_SEO_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $pagePath);
                    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                    while ($ob = $res->GetNextElement()) {
                        $arFieldsSeo['SEO']['PROPS'] = $ob->GetProperties();
                    }
                }
                else {
                    ShowError(GetMessage("WF_IBLOCK_ERROR"));
                }

                if (!empty($arFieldsSeo))
                    $obCache->EndDataCache(array('arFieldsSeo' => $arFieldsSeo));
                else
                    $obCache->AbortDataCache();
            }
            if (is_array($arFieldsSeo) and count($arFieldsSeo > 0))
                $pageSeoTexts = $arFieldsSeo['SEO']['PROPS']["WF_SEO_TEXT"]["VALUE"];
            else
                $pageSeoTexts = '';
            self::$seo = $pageSeoTexts; //SEO texts
        }
    }

    /**
     * Replace Seo Texts Macros for Current Page
     * @param string $contents
     */
    function ReplaceSeoTextsMacros(&$contents) {//Work OnEndBufferContent
        if (!strstr($_SERVER["REQUEST_URI"], '/bitrix/')) {
            $pageSeoTexts = self::$seo; //seo texts for current page
                if (is_array($pageSeoTexts) and count($pageSeoTexts > 0)) {
                    foreach ($pageSeoTexts as $sKey => $seoText) {
                        $contents = str_replace('#WF_SEO_TEXT_' . ($sKey + 1) . '#', htmlspecialchars_decode($seoText['TEXT']), $contents);
                    }
                }
                else {
                    $contents = preg_replace("/#WF_SEO_TEXT_\d+#/", "", $contents);
                }
        }
    }

}
?>