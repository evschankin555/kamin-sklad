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

if ($arParams["WF_JQUERY"]=="Y" or !isset($arParams["WF_JQUERY"]))
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

$curPage = CWebflySeo::GetPageDir();

$arResult = array(
  'ID' => '',
  'PAGE' => '',
  'TITLE' => '',
  'H1' => '',
  'ROBOTS' => '',
  'DESCRIPTION' => '',
  'KEYWORDS' => '',
);

if (isset($_REQUEST['wfSeoEditSave']) && $_REQUEST['wfSeoEditSave'] == 'Y' and $userRight == "admin")
{
    $element = new CIBlockElement;

    if (isset($_REQUEST['PageUrl']) && (strlen($_REQUEST['PageUrl']) > 0))
        $curPage = CSeoCities::GetDomain($_REQUEST['PageUrl'],'www.');

    $Title = (isset($_REQUEST['PageTitle'])) ? $_REQUEST['PageTitle'] : '';
    $Robots = (isset($_REQUEST['PageRobots'])) ? $_REQUEST['PageRobots'] : '';
    $Description = (isset($_REQUEST['PageDescription'])) ? $_REQUEST['PageDescription'] : '';
    $KeyWords = (isset($_REQUEST['PageKeywords'])) ? $_REQUEST['PageKeywords'] : '';
    $H1 = (isset($_REQUEST['PageH1'])) ? $_REQUEST['PageH1'] : '';

    $arSelect = Array(
      "ID",
      "IBLOCK_ID",
    );

    $arFilter = Array("IBLOCK_CODE" => WF_SEO_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $curPage);

    $res = $element->GetList(Array(), $arFilter, false, false, $arSelect)->fetch();
    $propWF_SEO = $element->getProperty($res["IBLOCK_ID"],$res["ID"],array("ID" => "ASC"),array("CODE" => "WF_SEO_TEXT"));
    $WF_SEO_TEXT = array();
    while($ar = $propWF_SEO->Fetch()){
        if ($ar["VALUE"])
            $WF_SEO_TEXT[] = $ar["VALUE"];
    }
    $PROP = array();
    $PROP["WF_TITLE"] = $Title;
    $PROP["WF_H1"] = $H1;
    $PROP["WF_ROBOTS"] = $Robots;
    $PROP["WF_DESCRIPTION"] = $Description;
    $PROP["WF_KEYWORDS"] = $KeyWords;
    $PROP["WF_SEO_TEXT"] = $WF_SEO_TEXT;

    $arFields = Array(
      "NAME" => $curPage,
      "PROPERTY_VALUES" => $PROP,
    );

    //update
    if (!empty($res))
    {
        $element->Update($_REQUEST['PageId'], $arFields);
        $elementID = $_REQUEST['PageId'];
        $iblockID = $res["IBLOCK_ID"];
        unset($PROP);
    }
    //add
    else
    {
        //get IBLOCK ID
        $res = CIBlock::GetList(
                Array(), Array(
              'CODE' => WF_SEO_IBLOCK,
              'ACTIVE' => 'Y',
                ), true
        );
        while ($ar_res = $res->Fetch()) {
            $iblockID = $ar_res['ID'];
        }

        $arFieldsDop = Array(
          "IBLOCK_ID" => $iblockID
        );
        $arFields = array_merge($arFields, $arFieldsDop);

        $elementID = ($element->Add($arFields));
        unset($PROP);
    }

    $this->clearResultCache(array($curPage, SITE_ID, $userRight));

    if (isset($_REQUEST['save_and_to_admin']))
    {
        LocalRedirect("/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $iblockID . "&type=wf_seocities&ID=" . $elementID . "&lang=" . LANGUAGE_ID . "&find_section_section=0&WF=Y");
    }
}

$arResult['PAGE'] = $curPage;

if ($this->StartResultCache(false, array($curPage, SITE_ID, $userRight)))
{
    $arSelect = Array(
      "ID",
      "IBLOCK_ID",
      "NAME",
      "PROPERTY_*",
    );

    $arFilter = Array("IBLOCK_CODE" => WF_SEO_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $curPage);

    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    while ($ob = $res->GetNextElement()) {
        $seoFields['FIELDS'] = $ob->GetFields();
        $seoFields['PROPS'] = $ob->GetProperties();
    };

    if (!empty($seoFields['FIELDS']["ID"]))
    {
        $arResult["ID"] = $seoFields['FIELDS']["ID"];
        $arResult["PAGE"] = $seoFields['FIELDS']["NAME"];
    }
    if (!empty($seoFields["PROPS"]))
    {
        if (!empty($seoFields["PROPS"]["WF_TITLE"]))
            $arResult["TITLE"] = $seoFields["PROPS"]["WF_TITLE"]["VALUE"];
        
        if (!empty($seoFields["PROPS"]["WF_H1"]))
            $arResult["H1"] = $seoFields["PROPS"]["WF_H1"]["VALUE"];

        if (!empty($seoFields["PROPS"]["WF_ROBOTS"]))
            $arResult["ROBOTS"] = $seoFields["PROPS"]["WF_ROBOTS"]["VALUE"];

        if (!empty($seoFields["PROPS"]["WF_DESCRIPTION"]))
            $arResult["DESCRIPTION"] = $seoFields["PROPS"]["WF_DESCRIPTION"]["VALUE"];

        if (!empty($seoFields["PROPS"]["WF_KEYWORDS"]))
            $arResult["KEYWORDS"] = $seoFields["PROPS"]["WF_KEYWORDS"]["VALUE"];
    }
    unset($seoFields);

    $this->SetResultCacheKeys(array(
      "ID",
      "TITLE",
      "H1",
      "ROBOTS",
      "KEYWORDS",
      "DESCRIPTION",
    ));

    $this->IncludeComponentTemplate();
}
?>