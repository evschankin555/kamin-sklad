<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
CModule::IncludeModule("sale");
?>
<?
foreach($arResult["ITEMS"] as $arItem){

	$arrLocation = array();
	$arrLocationNames = array();
    $saleLocationGroup = new CSaleLocationGroup();
    $db_res = $saleLocationGroup->GetLocationList(array("LOCATION_GROUP_ID"=>$arItem['PROPERTIES']['cities']["VALUE"]));
	while ($ar_res = $db_res->Fetch())
	{
		$arrLocation[]=$ar_res["LOCATION_ID"];
	}
	$db_vars = CSaleLocation::GetList(array(),array("CITY_ID" => $arrLocation,"LID" => LANGUAGE_ID),false,false,array());
	while ($vars = $db_vars->Fetch()):
		$arrLocationNames[] = $vars["CITY_NAME"];
	endwhile;

	$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]=array(
		"ID"=>$arItem['ID'],
		"NAME"=>$arItem['NAME'], 
		"TEXT"=>$arItem['PREVIEW_TEXT'], 
		"NAMESKLON"=>$arItem['PROPERTIES']['namesklon']['VALUE'], 
		"DOMAIN"=>$arItem['PROPERTIES']['domain']['VALUE'],
		"PAGE"=>$arItem['PROPERTIES']['ispage']['VALUE_XML_ID'],
		"PHONES"=>$arItem['PROPERTIES']['phones']['VALUE'],
		"ADDRESS"=>$arItem['PROPERTIES']['address']['VALUE'],
		"TIME"=>$arItem['PROPERTIES']['time']['VALUE'],
		"IPS"=>$arItem['PROPERTIES']['ips']['VALUE'],
		"SITIES"=>$arrLocationNames
		);
	if (strlen($arItem['PROPERTIES']['map']['VALUE'])>0){
		$arSplit=preg_split('#,#', $arItem['PROPERTIES']['map']['VALUE']);
		$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['map']['LAT']=$arSplit[0];
		$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['map']['LON']=$arSplit[1];
	}
	
	if ($_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['map']['LAT']<=0){
		$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['map']['LAT']=0;
	}
	if ($_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['map']['LON']<=0){
		$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['map']['LON']=0;
	}	
			
	$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['PRICES']['PRICE']=$arItem['PROPERTIES']['price']['VALUE'];		

	if (strlen($arItem['PROPERTIES']['price']['VALUE'])>0){
		$dbPriceType = CCatalogGroup::GetList(
		        array("SORT" => "ASC"),
		        array("NAME" => $arItem['PROPERTIES']['price']['VALUE'])
		    );			
		if ($dbPriceType->SelectedRowsCount()>0){
			$arPriceType = $dbPriceType->Fetch();
			$_SESSION['CURRENT_LOCATION']['LOCATIONS'][$arItem['ID']]['PRICES']['PRICE_ID']=$arPriceType['ID'];
		}    
	}

	
	if ($arItem['PROPERTIES']['DEFAULT_LOCATION']['VALUE']=='Y'){
		$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']=array(
			"ID"=>$arItem['ID'],
			"NAME"=>$arItem['NAME'], 
			"TEXT"=>$arItem['PREVIEW_TEXT'], 
			"NAMESKLON"=>$arItem['PROPERTIES']['namesklon']['VALUE'], 			
			"DOMAIN"=>$arItem['PROPERTIES']['domain']['VALUE'],
			"PAGE"=>$arItem['PROPERTIES']['ispage']['VALUE'],
			"PHONES"=>$arItem['PROPERTIES']['phones']['VALUE'],
			"ADDRESS"=>$arItem['PROPERTIES']['address']['VALUE'],
			"TIME"=>$arItem['PROPERTIES']['time']['VALUE'],	
			"IPS"=>$arItem['PROPERTIES']['ips']['VALUE'],					
			"SITIES"=>$arrLocationNames
			);

		if (strlen($arItem['PROPERTIES']['map']['VALUE'])>0){
			$arSplit=preg_split('#,#', $arItem['PROPERTIES']['map']['VALUE']);
			$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['map']['LAT']=$arSplit[0];
			$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['map']['LON']=$arSplit[1];
		}

		if ($_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['map']['LAT']<=0){
			$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['map']['LAT']=0;
		}
		if ($_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['map']['LON']<=0){
			$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['map']['LON']=0;
		}

		$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['PRICES']['PRICE']=$arItem['PROPERTIES']['price']['VALUE'];	
		
		if (strlen($arItem['PROPERTIES']['price']['VALUE'])>0){
			$dbPriceType = CCatalogGroup::GetList(
			        array("SORT" => "ASC"),
			        array("NAME" => $arItem['PROPERTIES']['price']['VALUE'])
			    );
			if ($dbPriceType->SelectedRowsCount()>0){
				$arPriceType = $dbPriceType->Fetch();
				$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT']['PRICES']['PRICE_ID']=$arPriceType['ID'];
			}    
		}		
		
	}
}