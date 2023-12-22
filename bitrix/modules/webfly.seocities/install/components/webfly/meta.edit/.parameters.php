<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
$arComponentParameters = array(
  "GROUPS" => array(),
  "PARAMETERS" => array(
    "CACHE_TIME" => Array("DEFAULT" => 3600),
    "WF_JQUERY" => array(
      "NAME" => GetMessage("WF_JQUERY"),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "Y"
    )
  ),
);
?>