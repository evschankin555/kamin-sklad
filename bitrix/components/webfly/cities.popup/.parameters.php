<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
if (!CModule::IncludeModule("iblock"))
    return;

//Favorites Props
$dbProps = CIBlockProperty::GetList(array(), array("IBLOCK_CODE" => 'webfly_cities', "ACTIVE_DATE" => "Y"));
$payprop = array();
while ($arProps = $dbProps->GetNext()) {
    if ($arProps['CODE'])
    {
        $payprop[$arProps['CODE']] = '[' . $arProps['CODE'] . '] ' . $arProps['NAME'];
    }
}

$arComponentParameters = array(
  "GROUPS" => array(
  ),
  "PARAMETERS" => array(
    "CACHE_TIME" => Array("DEFAULT" => 3600),
    "WF_FAVORITE" => array(
      "NAME" => GetMessage("WF_FAVORITE"),
      "TYPE" => "LIST",
      "VALUES" => $payprop,
      "SIZE" => 5,
    ),
    "WF_JQUERY" => array(
      "NAME" => GetMessage("WF_JQUERY"),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "N"
    )
  ),
);
?>