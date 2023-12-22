<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<?

CModule::IncludeModule("iblock");
CModule::IncludeModule("webfly.seocities");
?>
<?

if ($_POST["ID"]) {
    $baseDomain = CWebflyCities::GetBaseDomain();
    $subDomain = CWebflyCities::GetSubDomain();

    //Work with cache
    $obCache = new CPHPCache();
    $iLifeTime = 36000000;
    $cacheID = 'citiespopup_allcitiespopup' . SITE_ID . $_POST["ID"] . $_POST["LOCADD"];
    $cachePath = SITE_ID . WF_SEOCITIES_CACHEFOLDER . 'cities.popup/cities_popup/';
    if ($obCache->InitCache($iLifeTime, $cacheID, $cachePath)) {
        $vars = $obCache->GetVars();
        $result = $vars['result'];
    }
    elseif ($obCache->StartDataCache()) {
        $result = array();

        if ($_POST["LEVEL"] == 1) {//Click on first lvl section
            $lvl2 = CIBlockSection::GetList(array("sort" => "asc"), array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "SECTION_ID" => $_POST["ID"]), false, array("ID", "NAME"), false);

            while ($lvl2Ob = $lvl2->Fetch()) {
                $lvl2Sects[] = $lvl2Ob;
            }

            if ($lvl2Sects) {//have sections
                foreach ($lvl2Sects as $lvl2Sect)
                    $result["LOCATION"][] = array("ID" => $lvl2Sect["ID"], "NAME" => $lvl2Sect["NAME"]);
                $result["FLAG"] = '0';
            }
            else {//no sections, check elements
                $arSelect = Array(
                  "ID",
                  "IBLOCK_ID",
                  "NAME",
                  "PROPERTY_WF_SUBDOMAIN"
                );
                $arFilter = Array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "SECTION_ID" => $_POST["ID"]);
                $res = CIBlockElement::GetList(Array('NAME' => "ASC"), $arFilter, false, false, $arSelect);

                while ($ob = $res->GetNextElement()) {
                    $ptops = $ob->GetProperties();
                    $podd = $ptops['WF_SUBDOMAIN']['VALUE'] != '' ? $ptops['WF_SUBDOMAIN']['VALUE'] : 'default';
                    $cities[$podd]['FIELDS'] = $ob->GetFields();
                }
                if ($cities) {//have elements
                    foreach ($cities as $key => $val) {
                        $url = "";
                        // Build cities list
                        if ($key != $subDomain) {
                            if ($key != 'default')
                                $url = WF_SC_PROTOCOL . $key . '.' . $baseDomain . $_POST['LOCADD'];
                            else
                                $url = WF_SC_PROTOCOL . $baseDomain . $_POST['LOCADD'];
                        }
                        // Separate cities
                        if ($key != 'default')
                            $url = WF_SC_PROTOCOL . $key . '.' . $baseDomain . $_POST['LOCADD'];
                        else
                            $url = WF_SC_PROTOCOL . $baseDomain . $_POST['LOCADD'];

                        $result["LOCATION"][] = array("ID" => $val["FIELDS"]["ID"], "NAME" => $val["FIELDS"]["NAME"], "URL" => $url);
                    }
                    $result["FLAG"] = '1';
                }
            }
        }
        else {//if second lvl section, check elements
            $arSelect = Array(
              "ID",
              "IBLOCK_ID",
              "NAME",
              "PROPERTY_WF_SUBDOMAIN"
            );
            $arFilter = Array("IBLOCK_CODE" => WF_CITIES_IBLOCK, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "SECTION_ID" => $_POST["ID"]);
            $res = CIBlockElement::GetList(Array('NAME' => "ASC"), $arFilter, false, false, $arSelect);

            while ($ob = $res->GetNextElement()) {
                $ptops = $ob->GetProperties();
                $podd = $ptops['WF_SUBDOMAIN']['VALUE'] != '' ? $ptops['WF_SUBDOMAIN']['VALUE'] : 'default';
                $cities[$podd]['FIELDS'] = $ob->GetFields();
            }
            if ($cities) {//have elements
                foreach ($cities as $key => $val) {
                    $url = "";
                    // Build cities list
                    if ($key != $subDomain) {//region - default if 2 lvls
                        //echo print_r($_SERVER, true);
                        if ($key != 'default')
                            $url = WF_SC_PROTOCOL . $key . '.' . $baseDomain . $_POST['LOCADD'];
                        else
                            $url = WF_SC_PROTOCOL . $baseDomain . $_POST['LOCADD'];
                    }
                    // Separate cities
                    if ($key != 'default')
                        $url = WF_SC_PROTOCOL . $key . '.' . $baseDomain . $_POST['LOCADD'];
                    else
                        $url = WF_SC_PROTOCOL . $baseDomain . $_POST['LOCADD'];

                    $result["LOCATION"][] = array("ID" => $val["FIELDS"]["ID"], "NAME" => $val["FIELDS"]["NAME"], "URL" => $url);
                }
                $result["FLAG"] = '1';
            }
        }
        $obCache->EndDataCache(array('result' => $result));
    }
    if ($result)
        echo json_encode($result);
}
?>