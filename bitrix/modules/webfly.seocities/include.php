<?
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/webfly.seocities/config.php");

CModule::AddAutoloadClasses('webfly.seocities', array(
  'CWebflyIBElements' => 'classes/general/iblock_elements.php',
  'CWebflyCities' => 'classes/general/wf_cities.php',
  'CWebflySeo' => 'classes/general/wf_seo.php'
));

Class CSeoCities {

    /**
     * @param string $source - need to clean domain
     * @param string $ltrim - need to delete from start of string. It may be empty - ''
     * @return string $fullDomain
     */
    public function GetDomain($source, $ltrim) {
        $fullDomain = ltrim(str_replace(WF_SC_PROTOCOL, '', $source), $ltrim);
        return $fullDomain;
    }

    /**
     *
     * @return string - City ID from IB
     */
    public function getCityId() {
		if (isset($_SESSION['CURRENT_LOCATION']['CURRENT2']['ID']))
		{
			$subdomain = $_SESSION['CURRENT_LOCATION']['CURRENT2']['ID'];
			$obCache = new CPHPCache;
			$iLifeTime = 60 * 60 * 24 * 30;
			$sCacheIDS = 'cityID' . SITE_ID . $subdomain;
			if ($obCache->InitCache($iLifeTime, $sCacheIDS, SITE_ID . WF_SEOCITIES_CACHEFOLDER . 'citiesIDs/')) {
				$arVars = $obCache->GetVars();
				$cityID = $arVars['cityID'];
			}
			elseif ($obCache->StartDataCache()) {
				if (CModule::IncludeModule('iblock')) {
					$arSelect = Array(
					  "ID",
					  "IBLOCK_ID",
					  "NAME",
					  "PROPERTY_WF_SUBDOMAIN"
					);
	
					$arFilter = Array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $_SESSION['CURRENT_LOCATION']['CURRENT2']['NAME']);
					$resCity = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
					while ($obCity = $resCity->Fetch()) {
						$cityID = $obCity["ID"];
					}
				}
				$obCache->EndDataCache(array('cityID' => $cityID));
			}
		}
		else
		{
			$subdomain = CWebflyCities::GetSubDomain(); //current subdomain
			$obCache = new CPHPCache;
			$iLifeTime = 60 * 60 * 24 * 30;
			$sCacheIDS = 'cityID' . SITE_ID . $subdomain;
			if ($obCache->InitCache($iLifeTime, $sCacheIDS, SITE_ID . WF_SEOCITIES_CACHEFOLDER . 'citiesIDs/')) {
				$arVars = $obCache->GetVars();
				$cityID = $arVars['cityID'];
			}
			elseif ($obCache->StartDataCache()) {
				if (CModule::IncludeModule('iblock')) {
					$arSelect = Array(
					  "ID",
					  "IBLOCK_ID",
					  "NAME",
					  "PROPERTY_WF_SUBDOMAIN"
					);
	
					$arFilter = Array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "PROPERTY_WF_SUBDOMAIN" => ($subdomain == 'default' ? false : $subdomain));
					$resCity = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
					while ($obCity = $resCity->Fetch()) {
						$cityID = $obCity["ID"];
					}
				}
				$obCache->EndDataCache(array('cityID' => $cityID));
			}
		}		
		
        return $cityID;
    }

}

?>
