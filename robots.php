<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: text/plain');

if(!CModule::IncludeModule("iblock")){
	echo "failure";
	return;
}
$arSelect = Array("ID", "NAME", "DETAIL_TEXT");
$arFilter = Array("IBLOCK_ID"=>20, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_domain"=>$_SERVER['SERVER_NAME'],"!DETAIL_TEXT"=>false);
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
if($ar_fields = $res->GetNext())
{
	print_r($ar_fields["~DETAIL_TEXT"]);
}
else
{
	$arSelect = Array("ID", "NAME", "DETAIL_TEXT");
	$arFilter = Array("IBLOCK_ID"=>20, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", "PROPERTY_DEFAULT_LOCATION"=>"176","!DETAIL_TEXT"=>false);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
	if($ar_fields = $res->GetNext())
	{
		print_r($ar_fields["~DETAIL_TEXT"]);
	}
}