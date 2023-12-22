<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!isset($arParams["CACHE_TIME"]))
{
    $arParams["CACHE_TIME"] = 3600;
}

if ($USER->IsAdmin())
    $userRight = "admin";
else
    $userRight = "not_admin";

if ($arParams["WF_JQUERY"]=="Y")
    CJSCore::Init(array("jquery", "popup"));

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

$is_excelmodule_installed = \Bitrix\Main\Loader::includeModule('webfly.seocities');

if (!$is_excelmodule_installed)
{
    $this->AbortResultCache();
    ShowError("webfly.seocities MODULE IS NOT INSTALLED");
    return false;
}

if (!CModule::IncludeModule("iblock"))
{
    $this->AbortResultCache();
    ShowError("IBLOCK MODULE IS NOT INSTALLED");
    return;
}

$baseDomain = CWebflyCities::GetBaseDomain();
$subDomain = CWebflyCities::GetSubDomain();

//Work with cache
if ($arParams["WF_FAVORITE"])
    $favForCache = $arParams["WF_FAVORITE"];
else
    $favForCache = '';


$obCache = new CPHPCache;
$iLifeTime = $arParams["CACHE_TIME"];
$cacheID = 'citiespopup_allcities' . SITE_ID. $subDomain. $favForCache;
$cachePath = SITE_ID . WF_SEOCITIES_CACHEFOLDER . 'cities.popup/cities_array/';

if ($obCache->InitCache($iLifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    $arResult = $vars['cities'];
}
elseif ($obCache->StartDataCache())
{
    //First lvl section
    $lvl1 = CIBlockSection::GetList(array("sort"=>"asc"), array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "DEPTH_LEVEL" => 1), false, array("ID", "NAME"), false);
    while ($ob = $lvl1->Fetch()) {
        $arResult["FIRST_LEVEL_SECTIONS"][] = $ob;
    }

//All Cities
    $arSelect = Array(
      "ID",
      "IBLOCK_ID",
      "NAME",
      "PROPERTY_WF_SUBDOMAIN",
      "PROPERTY_" . $arParams["WF_FAVORITE"]//favorites
    );
    $arFilter = Array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");
    $res = CIBlockElement::GetList(Array('NAME' => "ASC"), $arFilter, false, false, $arSelect);
    while ($ob = $res->GetNextElement()) {
        $ptops = $ob->GetProperties();
        $podd = $ptops['WF_SUBDOMAIN']['VALUE'] != '' ? $ptops['WF_SUBDOMAIN']['VALUE'] : 'default';
        $cities[$podd]['FIELDS'] = $ob->GetFields();
    }

    foreach ($cities as $key => $val)
    {
        // Build cities list
        if ($key != $subDomain)
        {//echo print_r($_SERVER, true);
            if ($key != 'default')
                $cities[$key]["URL"] = WF_SC_PROTOCOL . $key . '.' . $baseDomain;
            else
                $cities[$key]["URL"] = WF_SC_PROTOCOL . $baseDomain;
        }
        // Separate cities
        if ($key != 'default')
            $cities[$key]["URL"] = WF_SC_PROTOCOL . $key . '.' . $baseDomain;
        else
            $cities[$key]["URL"] = WF_SC_PROTOCOL . $baseDomain;

        //Favorites
        if ($val["FIELDS"]["PROPERTY_" . $arParams["WF_FAVORITE"] . "_VALUE"])
        {
            $arResult["FAVORITES_CITIES"][$key] = $cities[$key];
        }
    }
//Current City
    if (!empty($cities[$subDomain]['FIELDS']))
    {
        $arResult["CURRENT_CITY"] = $cities[$subDomain]["FIELDS"];
    }
    $obCache->EndDataCache(array('cities' => $arResult));
}
$this->IncludeComponentTemplate();
?>