<?

IncludeModuleLangFile(__FILE__);

/**
 * class works with wf_cities IB
 */
class CWebflyCities {

    static $cities = array();

    public function __construct() {
    }

    /*     * Take Base domain from site settings
     * @return string $baseDomain
     */

    function GetBaseDomain() {
        $baseDomain = false;
        if (SITE_SERVER_NAME)
            $baseDomain = CSeoCities::GetDomain(SITE_SERVER_NAME, 'www.');
        if (!$baseDomain)
            $baseDomain = self::GetBaseDomainOld();
        return $baseDomain;
    }

    /**
     * Get Base domain, if SITE_SERVER_NAME in site settings is empty
     * works only for subdomains like msk.site.ru
     * doesn't work for subdomains like msk.site.site.ru
     * @return string
     */
    function GetBaseDomainOld() {
        $baseDomain = false;
        $domain = CSeoCities::GetDomain($_SERVER["HTTP_HOST"], 'www.');
        $domain = preg_replace('(:+\d+)', '', $domain); //NEW! delete port
        $domainChain = explode(".", $domain);
        if (count($domainChain) == 2)
            $region = 'default';
        else
            $region = $domainChain[0];
        $baseDomain = str_replace($region . '.', '', $domain);
        return $baseDomain;
    }

    /**
     * Get current subdomain (msk, vrn, etc.)
     * or default if this is base domain
     * @return string $subdomain
     */
    function GetSubDomain() {
        $fullDomain = CSeoCities::GetDomain($_SERVER["HTTP_HOST"], 'www.'); //without www (site.ru or msk.site.ru)
        $fullDomain = preg_replace('(:+\d+)', '', $fullDomain); //NEW! delete port
        $baseDomain = self::GetBaseDomain();
        $subdomain = '';

        //$baseDomain - base domain without http, https, www and regional subdomain: site.ru
        if ($baseDomain) {
            if ($fullDomain == $baseDomain)
                $subdomain = 'default';
            else
                $subdomain = trim(strstr($fullDomain, $baseDomain, true), '.');
        }
        //old version. If empty $baseDomain do explode -
        //works only for subdomains like msk.site.ru
        //doesn't work for subdomains like msk.site.site.ru
        else {
            $partsDomain = explode('.', $fullDomain);
            if (count($partsDomain) == 2)
                $subdomain = 'default';
            else
                $subdomain = $partsDomain[0];
        }
        return $subdomain;
    }

    /**
     * Get fields and props from all wf_citites IB elements
     * @return array $arFieldsCity - info about all elements from wf_citites IB
     */
    function GetWFCitiesInfo() {//Work OnEpilog?
        if (!strstr($_SERVER["REQUEST_URI"], '/bitrix/')) {
            $obCache = new CPHPCache;
            $iLifeTime = 60 * 60 * 24 * 30;
            $sCacheIDC = 'cities' . SITE_ID;
            if ($obCache->InitCache($iLifeTime, $sCacheIDC, SITE_ID . WF_SEOCITIES_CACHEFOLDER . WF_CITIES_IBLOCK . '/')) {
                $arVarsCity = $obCache->GetVars();
                $arFieldsCity = $arVarsCity['arFieldsCity'];
            }
            elseif ($obCache->StartDataCache()) {
                if (CModule::IncludeModule('iblock')) {
                    $arSelect = Array(
                      "ID",
                      "IBLOCK_ID",
                      "NAME",
                      'PREVIEW_TEXT',
                      "PROPERTY_*",
                    );
                    $arFilter = Array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");
                    $res = CIBlockElement::GetList(Array('NAME' => "ASC"), $arFilter, false, false, $arSelect);
                    while ($ob = $res->GetNextElement()) {
                        $cityProps = $ob->GetProperties();
                        $cityV = $ob->GetFields();
                       // $subdomain = $cityProps['WF_SUBDOMAIN']['VALUE'] != '' ? $cityProps['WF_SUBDOMAIN']['VALUE'] : 'default';
                        $subdomain = $cityV['NAME'];
                        $arFieldsCity['CITY'][$subdomain]['FIELDS'] = $cityV;
                        $arFieldsCity['CITY'][$subdomain]['PROPS'] = $cityProps;
                    }
                }
                else {
                    ShowError(GetMessage("WF_IBLOCK_ERROR"));
                }
                $obCache->EndDataCache(array('arFieldsCity' => $arFieldsCity));
            }
            if (is_array($arFieldsCity) and count($arFieldsCity > 0))
                $arFieldsCity = $arFieldsCity['CITY'];
            self::$cities = $arFieldsCity;
        }
    }

    /**
     * Start to replace current domain Macroces
     * @param string $contents - page buffered content
     * @param string $subdomain - current subdomain
     * @param array $allCititesInfo - info about all elements from wf_citites IB
     */
    function ReplaceRegionMacrosProcess(&$contents, $subdomain, $allCititesInfo) {
        self::ReplaceCurCityName($contents, $subdomain, $allCititesInfo);
    }

    /**
     * Replace WF_CITY_NAME macros
     * @param string $contents - page buffered content
     * @param string $subdomain - current subdomain
     * @param array $allCititesInfo - info about all elements from wf_citites IB
     */
    function ReplaceCurCityName(&$contents, $subdomain, $allCititesInfo) {
        if (!empty($allCititesInfo[$subdomain]['FIELDS']['NAME']) and substr_count($contents, '#WF_CITY_NAME#') > 0) {
            $contents = str_replace('#WF_CITY_NAME#', htmlspecialchars_decode($allCititesInfo[$subdomain]['FIELDS']['NAME']), $contents);
        }
        self::ReplaceCurCityProps($contents, $subdomain, $allCititesInfo);
    }

    /**
     * Replace citie's props macros
     * @param string $contents - page buffered content
     * @param string $subdomain - current subdomain
     * @param array $allCititesInfo - info about all elements from wf_citites IB
     */
    private function ReplaceCurCityProps(&$contents, $subdomain, $allCititesInfo) {
        if (!empty($allCititesInfo[$subdomain]['PROPS'])) {
            foreach ($allCititesInfo[$subdomain]['PROPS'] as $propCode => $propVal) {
                $replacement = '';
                if (!is_array($propVal['VALUE'])) {
                    $replacement = $propVal['VALUE'];
                    if ($propVal['VALUE'] == '' or empty($propVal['VALUE']))
                        $replacement = $allCititesInfo['default']['PROPS'][$propCode]['VALUE'];
                }
                elseif (array_key_exists('TEXT', $propVal['VALUE'])) {
                    $replacement = $propVal['VALUE']['TEXT'];
                    if ($propVal['VALUE']['TEXT'] == '' or empty($propVal['VALUE']['TEXT']))
                        $replacement = $allCititesInfo['default']['PROPS'][$propCode]['VALUE']['TEXT'];
                }
                elseif (is_array($propVal['VALUE'])) {
                    if (count($propVal['VALUE']) == 1) {
                        $replacement = $propVal['VALUE'][0];
                        if ($propVal['VALUE'][0] == '' or empty($propVal['VALUE'][0]))
                            $val['VALUE'][0] = $allCititesInfo['default']['PROPS'][$propCode]['VALUE'][0];
                    }
                    else {
                        if (!empty($propVal['VALUE'])) {
                            foreach ($propVal['VALUE'] as $subPropId => $subPropVal) {
                                $replacementItem = $subPropVal;
                                if ($subPropVal == '' or empty($subPropVal))
                                    $replacementItem = $allCititesInfo['default']['PROPS'][$propCode]['VALUE'][$subPropId];
                                $replacement .= $replacementItem . ', ';
                            }
                            $replacement = rtrim($replacement, ', ');
                        }
                    }
                }
                $contents = str_replace('#' . $propCode . '#', htmlspecialchars_decode($replacement), $contents);
            }
        }
        self::ReplaceAllCitiesPopup($contents, $subdomain, $allCititesInfo);
    }

    /**
     * Replace popup with all citites
     * @param string $contents - page buffered content
     * @param string $subdomain - current subdomain
     * @param array $allCititesInfo - info about all elements from wf_citites IB
     */
    function ReplaceAllCitiesPopup(&$contents, $subdomain, $allCititesInfo) {
        if (substr_count($contents, '#WF_CITIES#') > 0) {
            global $APPLICATION;
            $citiesPopup = '<link rel="stylesheet" href="/bitrix/css/webfly.seocities/popup.css"/>';
            $baseDomain = self::GetBaseDomain();
            $citiesPopup .= "
<div class='region'>
    " . GetMessage("WF_REGION") . ": <a class='region_name'>" . $allCititesInfo[$subdomain]['FIELDS']['NAME'] . "</a>
	<div class='select-city'>";
            if (!empty($allCititesInfo)) {
                foreach ($allCititesInfo as $sdomain => $domainInfo) {
                    // Build cities list
                    if ($sdomain != $subdomain) {
                        if ($sdomain != 'default')
                            $citiesPopup .= '<a href="' . WF_SC_PROTOCOL . $sdomain . '.' . $baseDomain . $_SERVER['REQUEST_URI'] . '">' . $domainInfo['FIELDS']['NAME'] . '</a>';
                        else
                            $citiesPopup .= '<a href="' . WF_SC_PROTOCOL . $baseDomain . $_SERVER['REQUEST_URI'] . '">' . $domainInfo['FIELDS']['NAME'] . '</a>';
                    }
                }
            }
            $citiesPopup .= '</div></div>';
        }else {
            $citiesPopup = '';
        }
        $contents = str_replace('#WF_CITIES#', $citiesPopup, $contents);
    }

    /**
     * Parent function for replace cities content on page
     * @param string $contents - page buffered content
     */
    function ReplaceRegionMacros(&$contents) {//Work OnEndBufferContent
        if (!strstr($_SERVER["REQUEST_URI"], '/bitrix/')) {
            $subdomain = self::GetSubDomain();
			$subdomain = $_SESSION['CURRENT_LOCATION']['CURRENT2']['NAME'];
            $allCititesInfo = self::$cities;
			
            if ($subdomain and ( is_array($allCititesInfo)) and count($allCititesInfo > 0))
                self::ReplaceRegionMacrosProcess($contents, $subdomain, $allCititesInfo);
        }
    }

}
?>