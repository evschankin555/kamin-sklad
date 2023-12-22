<?
class CWebflyIBElements {

    function ClearURLAdd(&$arFields) {
        $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
        if ($ar_res = $res->Fetch())
            $iblockCode = $ar_res["CODE"];
        if ($iblockCode == WF_SEO_IBLOCK) {
            $arFields["NAME"] = trim(str_replace(array('http://', 'https://'), '', $arFields["NAME"]));
        }
    }

    function ClearURLUpdate(&$arFields) {
        $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
        if ($ar_res = $res->Fetch())
            $iblockCode = $ar_res["CODE"];
        if ($iblockCode == WF_SEO_IBLOCK) {
            $arFields["NAME"] = trim(str_replace(array('http://', 'https://'), '', $arFields["NAME"]));
        }
    }

}
?>
