<?

global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang . "/lang/", "/install/index.php"));

Class webfly_seocities extends CModule {

    var $MODULE_ID = 'webfly.seocities';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function webfly_seocities() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("WF_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("WF_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("WF_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("WF_PARTNER_URI");
    }

    function InstallDB($arParams = array()) {
        global $DB, $DBType, $APPLICATION;
        RegisterModuleDependences('main', 'OnEndBufferContent', 'webfly.seocities', 'CWebflySeo', 'ReplaceSeoTextsMacros');
        RegisterModuleDependences('main', 'OnEndBufferContent', 'webfly.seocities', 'CWebflyCities', 'ReplaceRegionMacros');
        RegisterModuleDependences('main', 'OnEpilog', 'webfly.seocities', 'CWebflySeo', 'GetPageSeoTexts');
        RegisterModuleDependences('main', 'OnEpilog', 'webfly.seocities', 'CWebflyCities', 'GetWFCitiesInfo');
        RegisterModuleDependences('iblock', 'OnBeforeIBlockElementAdd', 'webfly.seocities', 'CWebflyIBElements', 'ClearURLAdd');
        RegisterModuleDependences('iblock', 'OnBeforeIBlockElementUpdate', 'webfly.seocities', 'CWebflyIBElements', 'ClearURLUpdate');
        return true;
    }

    function UnInstallDB($arParams = array()) {
        global $DB, $DBType, $APPLICATION;
        UnRegisterModuleDependences('main', 'OnEpilog', 'webfly.seocities', 'CSeoCities', 'goroddo');
        UnRegisterModuleDependences('main', 'OnEndBufferContent', 'webfly.seocities', 'CSeoCities', 'cities');
        UnRegisterModuleDependences('iblock', 'OnBeforeIBlockElementAdd', 'webfly.seocities', 'elementClear', 'ClearURLAdd');
        UnRegisterModuleDependences('iblock', 'OnBeforeIBlockElementUpdate', 'webfly.seocities', 'elementClear', 'ClearURLUpdate');
        
        UnRegisterModuleDependences('main', 'OnEndBufferContent', 'webfly.seocities', 'CWebflySeo', 'ReplaceSeoTextsMacros');
        UnRegisterModuleDependences('main', 'OnEndBufferContent', 'webfly.seocities', 'CWebflyCities', 'ReplaceRegionMacros');
        UnRegisterModuleDependences('main', 'OnEpilog', 'webfly.seocities', 'CWebflySeo', 'GetPageSeoTexts');
        UnRegisterModuleDependences('main', 'OnEpilog', 'webfly.seocities', 'CWebflyCities', 'GetWFCitiesInfo');
        UnRegisterModuleDependences('iblock', 'OnBeforeIBlockElementAdd', 'webfly.seocities', 'CWebflyIBElements', 'ClearURLAdd');
        UnRegisterModuleDependences('iblock', 'OnBeforeIBlockElementUpdate', 'webfly.seocities', 'CWebflyIBElements', 'ClearURLUpdate');
        return true;
    }

    function InstallEvents() {
        return true;
    }

    function UnInstallEvents() {
        return true;
    }

    function InstallFiles($arParams = array()) {
        return true;
    }

    function UnInstallFiles() {

        return true;
    }

    function DoInstall() {
        global $APPLICATION;
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/webfly.seocities/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/wizard.php"))
            require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/wizard.php");
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/install/wizard_sol/utils.php"))
            require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/install/wizard_sol/utils.php");

        if (!CModule::IncludeModule('iblock'))
            return false;
        /* ADD Iblock Type */
        $iblockType = "wf_seocities";

        $arType = array(
          "ID" => $iblockType,
          "SECTIONS" => "Y",
          "IN_RSS" => "N",
          "SORT" => 1000,
          "LANG" => array(
            "ru" => array(
              'NAME' => GetMessage("IBLOCK_TYPE_NAME"),
              'ELEMENT_NAME' => GetMessage("IBLOCK_TYPE_EL_NAME"),
              'SECTION_NAME' => GetMessage("IBLOCK_TYPE_SECT_NAME")
            ),
            "en" => array(
              'NAME' => GetMessage("IBLOCK_TYPE_NAME_EN"),
              'ELEMENT_NAME' => GetMessage("IBLOCK_TYPE_EL_NAME_EN"),
              'SECTION_NAME' => GetMessage("IBLOCK_TYPE_SECT_NAME_EN")
            )
          )
        );

        $cIblockType = new CIBlockType;
        $dbType = $cIblockType->GetList(Array(), Array("=ID" => $arType["ID"]));

        if (!$dbType->Fetch())
            $cIblockType->Add($arType);

        $arSites = array();
        $rsSites = CSite::GetList($by = "sort", $order = "desc", Array("ACTIVE" => "Y"));
        while ($arSite = $rsSites->Fetch()) {
            $arSites[] = $arSite["ID"];
        }

        /* Add Infoblock Function */

        function installIblocks($iblockCode, $iblockXMLFile, $propCodes, $iblockType = "wf_seocities") {
            unset($PROPS);
            $rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
            $iblockID = false;

            if ($arIBlock = $rsIBlock->Fetch())
                $iblockID = $arIBlock["ID"];

            if ($iblockID == false)
            {
                $permissions = Array(
                  "1" => "X",
                  "2" => "R"
                );
                $dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
                if ($arGroup = $dbGroup->Fetch())
                {
                    $permissions[$arGroup["ID"]] = 'W';
                };

                $iblockID = WizardServices::ImportIBlockFromXML(
                        $iblockXMLFile, $iblockCode, $iblockType, $arSites, $permissions
                );

                if ($iblockID < 1)
                    return;

                foreach ($propCodes as $code)
                {
                    $result = CIBlockProperty::GetList(array("order" => "asc"), array("IBLOCK_ID" => $iblockID, "CODE" => $code))->Fetch();
                    $PROPS["PRODUCT_PROP_" . $code] = $result["ID"];
                }
                if ($iblockCode == "webfly_cities")
                {
                    $listSettings = array("columns"=>"NAME,ACTIVE,PROPERTY_".$PROPS["PRODUCT_PROP_WF_SUBDOMAIN"].",PROPERTY_".$PROPS["PRODUCT_PROP_WF_PHONES"].",ID", "by"=>"name", "order"=>"asc", "page_size"=>"20");
                    $iblockSettings = array('tabs' => "edit1--#--" . GetMessage("OPTION_CITY_1") . "--,--ACTIVE--#--" . GetMessage("OPTION_CITY_2") . "--,--SORT--#--" . GetMessage("OPTION_CITY_3") . "--,--NAME--#--" . GetMessage("OPTION_CITY_4") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_SUBDOMAIN"] . "--#--" . GetMessage("OPTION_CITY_5") . "--,--edit1_csection1--#----" . GetMessage("OPTION_CITY_6") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_PHONES"] . "--#--" . GetMessage("OPTION_CITY_7") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_EMAIL"] . "--#--" . GetMessage("OPTION_CITY_8") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_CONTACTS"] . "--#--" . GetMessage("OPTION_CITY_9") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_MAP"] . "--#--" . GetMessage("OPTION_CITY_10") . "--,--edit1_csection2--#----" . GetMessage("OPTION_CITY_11") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_CITY_ROD"] . "--#--" . GetMessage("OPTION_CITY_12") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_CITY_VIN"] . "--#--" . GetMessage("OPTION_CITY_13") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_CITY_DAT"] . "--#--" . GetMessage("OPTION_CITY_14") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_CITY_TVOR"] . "--#--" . GetMessage("OPTION_CITY_15") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_CITY_PRED"] . "--#--" . GetMessage("OPTION_CITY_16") . "--,--edit1_csection3--#----" . GetMessage("OPTION_CITY_17") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_META"] . "--#--" . GetMessage("OPTION_CITY_18") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_COUNT"] . "--#--" . GetMessage("OPTION_CITY_19") . "--;--");
                }
                if ($iblockCode == "webfly_seo")
                {
                    $listSettings = array("columns"=>"NAME,ACTIVE,PROPERTY_".$PROPS["PRODUCT_PROP_WF_TITLE"].",ID", "by"=>"name", "order"=>"asc", "page_size"=>"20");
                    $iblockSettings = array('tabs' => "edit1--#--" . GetMessage("OPTION_CITY_1") . "--,--ACTIVE--#--" . GetMessage("OPTION_CITY_2") . "--,--SORT--#--" . GetMessage("OPTION_CITY_3") . "--,--NAME--#--" . GetMessage("OPTION_CITY_20") . "--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_TITLE"] . "--#--TITLE--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_ROBOTS"] . "--#--ROBOTS--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_DESCRIPTION"] . "--#--DESCRIPTION--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_KEYWORDS"] . "--#--KEYWORDS--,--PROPERTY_" . $PROPS["PRODUCT_PROP_WF_SEO_TEXT"] . "--#--" . GetMessage("OPTION_CITY_21") . "--;--");
                }

                //IBlock fields
                $iblock = new CIBlock;
                $arFields = Array(
                  "ACTIVE" => "Y",
                  "FIELDS" => array('IBLOCK_SECTION' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'ACTIVE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y',),
                    'ACTIVE_FROM' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'ACTIVE_TO' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'SORT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '0',),
                    'NAME' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '',),
                    'PREVIEW_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', "COMPRESSION" => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', "USE_WATERMARK_TEXT" => "N", "WATERMARK_TEXT" => "", "WATERMARK_TEXT_FONT" => "", "WATERMARK_TEXT_COLOR" => "", "WATERMARK_TEXT_SIZE" => "", "WATERMARK_TEXT_POSITION" => "tl", "USE_WATERMARK_FILE" => "N", "WATERMARK_FILE" => "", "WATERMARK_FILE_ALPHA" => "", "WATERMARK_FILE_POSITION" => "tl", "WATERMARK_FILE_ORDER" => NULL),),
                    'PREVIEW_TEXT_TYPE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text',),
                    'PREVIEW_TEXT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'DETAIL_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', "METHOD" => "resample", "COMPRESSION" => 95, "USE_WATERMARK_TEXT" => "N", "WATERMARK_TEXT" => "", "WATERMARK_TEXT_FONT" => "", "WATERMARK_TEXT_COLOR" => "", "WATERMARK_TEXT_SIZE" => "", "WATERMARK_TEXT_POSITION" => "tl", "USE_WATERMARK_FILE" => "N", "WATERMARK_FILE" => "", "WATERMARK_FILE_ALPHA" => "", "WATERMARK_FILE_POSITION" => "tl", "WATERMARK_FILE_ORDER" => NULL),),
                    'DETAIL_TEXT_TYPE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text',),
                    'DETAIL_TEXT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'XML_ID' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'CODE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '-', 'TRANS_OTHER' => '-', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N'),),
                    'TAGS' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '',),
                    'SECTION_NAME' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '',),),
                  'SECTION_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array("FROM_DETAIL" => "N", "SCALE" => "N", "WIDTH" => "", "HEIGHT" => "", "IGNORE_ERRORS" => "N", "METHOD" => "resample", "COMPRESSION" => 95, "DELETE_WITH_DETAIL" => "N", "UPDATE_WITH_DETAIL" => "N", "USE_WATERMARK_TEXT" => "N", "WATERMARK_TEXT" => "", "WATERMARK_TEXT_FONT" => "", "WATERMARK_TEXT_COLOR" => "", "WATERMARK_TEXT_SIZE" => "", "WATERMARK_TEXT_POSITION" => "tl", "USE_WATERMARK_FILE" => "N", "WATERMARK_FILE" => "", "WATERMARK_FILE_ALPHA" => "", "WATERMARK_FILE_POSITION" => "tl", "WATERMARK_FILE_ORDER" => NULL)),
                  'SECTION_DESCRIPTION_TYPE' => array('IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text'),
                  'SECTION_DESCRIPTION' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
                  'SECTION_DETAIL_PICTURE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array('SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', "METHOD" => "resample", "COMPRESSION" => 95, "USE_WATERMARK_TEXT" => "N", "WATERMARK_TEXT" => "", "WATERMARK_TEXT_FONT" => "", "WATERMARK_TEXT_COLOR" => "", "WATERMARK_TEXT_SIZE" => "", "WATERMARK_TEXT_POSITION" => "tl", "USE_WATERMARK_FILE" => "N", "WATERMARK_FILE" => "", "WATERMARK_FILE_ALPHA" => "", "WATERMARK_FILE_POSITION" => "tl", "WATERMARK_FILE_ORDER" => NULL),),
                  'SECTION_XML_ID' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => ''),
                  'SECTION_CODE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array("UNIQUE" => "N", "TRANSLITERATION" => "N", "TRANS_LEN" => 100, "TRANS_CASE" => "L", 'TRANS_SPACE' => '-', 'TRANS_OTHER' => '-', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N')),
                  'LOG_SECTION_ADD' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL),
                  'LOG_SECTION_EDIT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL),
                  'LOG_SECTION_DELETE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL),
                  'LOG_ELEMENT_ADD' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL),
                  'LOG_ELEMENT_EDIT' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL),
                  'LOG_ELEMENT_DELETE' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL),
                  'XML_IMPORT_START_TIME' => array('IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => NULL, "VISIBLE" => 'N'),
                  "CODE" => $iblockCode,
                  "XML_ID" => $iblockCode,
                  "NAME" => $iblock->GetArrayByID($iblockID, "NAME"),
                );

                $iblock->Update($iblockID, $arFields);
            }
            else
            {
                $arSites = array();
                $db_res = CIBlock::GetSite($iblockID);
                while ($res = $db_res->Fetch())
                    $arSites[] = $res["LID"];
                $iblock = new CIBlock;
                $iblock->Update($iblockID, array("LID" => $arSites));
            }
            CUserOptions::SetOption("form", "form_element_" . $iblockID, $iblockSettings);
            $md5 = md5($iblockType.".".$iblockID);
            CUserOptions::SetOption("list", "tbl_iblock_list_" . $md5, $listSettings);
        }

        /* Cities Infoblock */
        $iblockCodeCity = "webfly_cities";
        $iblockXMLFileCity = __DIR__ . "/xml/ru/webfly_cities.xml";
        $propCodesCity = array("WF_SUBDOMAIN", "WF_PHONES", "WF_EMAIL", "WF_CONTACTS", "WF_META", "WF_COUNT", "WF_MAP", "WF_CITY_ROD", "WF_CITY_PRED", "WF_CITY_VIN", "WF_CITY_TVOR", "WF_CITY_DAT");
        installIblocks($iblockCodeCity, $iblockXMLFileCity, $propCodesCity);

        /* SEO Infoblock */
        $iblockCodeSEO = "webfly_seo";
        $iblockXMLFileSEO = __DIR__ . "/xml/ru/webfly_seo.xml";
        $propCodesSeo = array("WF_TITLE", "WF_DESCRIPTION", "WF_KEYWORDS", "WF_SEO_TEXT", "WF_ROBOTS");
        installIblocks($iblockCodeSEO, $iblockXMLFileSEO, $propCodesSeo);

        $this->InstallFiles();
        $this->InstallDB();

        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall() {
        global $APPLICATION;
        $this->UnInstallDB();
        $this->UnInstallFiles();
        DeleteDirFilesEx("/bitrix/components/webfly/webfly.seocities");
        UnRegisterModule($this->MODULE_ID);
    }

}
