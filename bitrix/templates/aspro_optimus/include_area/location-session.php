<?
global $APPLICATION;
use Bitrix\Main\Diag\Debug; 
?>
<?$APPLICATION->IncludeComponent("bitrix:news.list","locations-list-session",Array(
        "DISPLAY_DATE" => "N",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "N",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "AJAX_MODE" => "N",
        "IBLOCK_TYPE" => "regions",
        "IBLOCK_ID" => "20",
        "NEWS_COUNT" => "100",
        "SORT_BY1" => "SORT",
        "SORT_ORDER1" => "ASC",
        "SORT_BY2" => "NAME",
        "SORT_ORDER2" => "ASC",
        "FILTER_NAME" => "",
        "FIELD_CODE" => Array(""),
        "PROPERTY_CODE" => Array("DEFAULT_LOCATION"),
        "CHECK_DATES" => "N",
        "DETAIL_URL" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_LAST_MODIFIED" => "N",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "PARENT_SECTION" => "0",
        "PARENT_SECTION_CODE" => "",
        "INCLUDE_SUBSECTIONS" => "Y",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "3600",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => "Новости",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => "",
        "PAGER_BASE_LINK" => "",
        "PAGER_PARAMS_NAME" => "arrPager",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => ""
    )
);?>
<?
function fn_getLocationDataByCookie(){
	if ($_COOKIE['CURRENT_LOCATION_ID']>0){
		foreach ($_SESSION['CURRENT_LOCATION']['LOCATIONS'] as $key => $arLocation){
		    if ($_COOKIE['CURRENT_LOCATION_ID']==$arLocation['ID']){
		    	$_SESSION['CURRENT_LOCATION']['CURRENT']=$arLocation;
		    	$_SESSION['CURRENT_LOCATION']['CURRENT2']=$arLocation;
		    	$_SESSION['CURRENT_LOCATION']['CURRENT']['APPROVE']='Y';
		    	$_SESSION["OFFICE"]["ID"]=0;
		    	
		    	$arrCityLocationFilter=array('LOGIC'=>'OR', 
		    					array('PROPERTY_CITY'=>false), 
		    					array('PROPERTY_CITY'=>$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'])
		    	);		
		    	$_SESSION['CURRENT_LOCATION']['CURRENT']['FILTER']=$arrCityLocationFilter;		
		    }
		}
	}
}

function fn_changeLocation(){
	$new_location = intval($_REQUEST['changeloc']);
	//Debug::writeToFile($APPLICATION->sDirPath, 'new_location', $APPLICATION->sDirPath.'debug.txt'); 
	if ($new_location>0){
        if( in_array(1,CUser::GetUserGroup(CUser::GetID()))||in_array(10,CUser::GetUserGroup(CUser::GetID())))
        {
    		if ($new_location!=$_SESSION['CURRENT_LOCATION']['CURRENT']['ID']){
    			foreach ($_SESSION['CURRENT_LOCATION']['LOCATIONS'] as $key => $arLocation){
    				if ($new_location==$arLocation['ID']){
    					$_SESSION['CURRENT_LOCATION']['CURRENT']=$arLocation;
    					$_SESSION['CURRENT_LOCATION']['CURRENT2']=$arLocation;
    					$_SESSION['CURRENT_LOCATION']['CURRENT']['APPROVE']='Y';
    					$_SESSION["OFFICE"]["ID"]=0;				
    					
    					setcookie("CURRENT_LOCATION_ID","0",time()-1, "/");					
    					
    					setcookie("CURRENT_LOCATION_ID",$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'],time()+86000,"/");	

    					$arrCityLocationFilter=array('LOGIC'=>'OR', 
    									array('PROPERTY_CITY'=>false), 
    									array('PROPERTY_CITY'=>$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'])
    					);		
    					$_SESSION['CURRENT_LOCATION']['CURRENT']['FILTER']=$arrCityLocationFilter;					
    				}
    			}
    		}
        }
	}	else{
		fn_getLocationDataByCookie();
	}
}

fn_changeLocation();

//Debug::dump($arLocation, "arLocation:");
if ($_SERVER["SERVER_NAME"]!="kamin-sklad.ru" && (COption::GetOptionString("main","redirectdomain")=="N" || $_SESSION["SESS_SEARCHER_ID"]==3 || $_SESSION["SESS_SEARCHER_ID"]==15 || $_SESSION["SESS_SEARCHER_ID"]==2 || $_SESSION["SESS_SEARCHER_ID"]==108 || $_SESSION["SESS_SEARCHER_ID"]==147 || $_SESSION["SESS_SEARCHER_ID"]==159 || $_SESSION["SESS_SEARCHER_ID"]==210))
{
	foreach ($_SESSION['CURRENT_LOCATION']['LOCATIONS'] as $key => $arLocation){
		if ($_SERVER["SERVER_NAME"]==$arLocation["DOMAIN"]){
			$_SESSION['CURRENT_LOCATION']['CURRENT']=$arLocation;
			$_SESSION['CURRENT_LOCATION']['CURRENT']['APPROVE']='Y';
			$_SESSION["OFFICE"]["ID"]=0;				
			setcookie("CURRENT_LOCATION_ID","0",time()-1, "/");								
			setcookie("CURRENT_LOCATION_ID",$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'],time()+86000,"/");	
			$arrCityLocationFilter=array('LOGIC'=>'OR', 
							array('PROPERTY_CITY'=>false), 
							array('PROPERTY_CITY'=>$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'])
			);		
			$_SESSION['CURRENT_LOCATION']['CURRENT']['FILTER']=$arrCityLocationFilter;					
		}
	}
}	
else
{	
	$cbip = fn_geoLocation();
	//Debug::dump($cbip, "cbip:");
	if ($_SESSION['CURRENT_LOCATION']['CURRENT']['APPROVE']!='Y'){
		$isset=false;
		foreach($_SESSION['CURRENT_LOCATION']['LOCATIONS'] as $key => $arLocation){
			if ($key=='DEFAULT') {continue;}

			if (in_array($_SERVER['REMOTE_ADDR'],$arLocation['IPS'])){
					$_SESSION['CURRENT_LOCATION']['CURRENT_IP']=$arLocation;
					$isset=true;
			}
		}	
		
		if (!$isset)		
		{
			$CityByIP = fn_geoLocation();
			if (strlen($CityByIP)>0){
				foreach($_SESSION['CURRENT_LOCATION']['LOCATIONS'] as $key => $arLocation){
					if ($key=='DEFAULT') {continue;}
		
					if ($arLocation['NAME']==$CityByIP || in_array($CityByIP,$arLocation['SITIES'])  ){
							$_SESSION['CURRENT_LOCATION']['CURRENT_IP']=$arLocation;
					}
				}
				$_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']=="N";
			} else {
				unset($_SESSION['CURRENT_LOCATION']['CURRENT_IP']);

			}
			//Debug::dump($arLocation, "arLocation:");
		}
	
		if ($_SESSION['CURRENT_LOCATION']['CURRENT']['ID']<1){
			
			if ($_SESSION['CURRENT_LOCATION']['CURRENT_IP']['ID']>0){
				$_SESSION['CURRENT_LOCATION']['CURRENT']=$_SESSION['CURRENT_LOCATION']['CURRENT_IP'];	
				$_SESSION['CURRENT_LOCATION']['CURRENT2']=$_SESSION['CURRENT_LOCATION']['CURRENT_IP'];	
	
				$_SESSION['CURRENT_LOCATION']['CURRENT']['APPROVE']='Y';
				setcookie("CURRENT_LOCATION_ID",$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'],time()+86000,"/");	
	
				if ($_SERVER["SERVER_NAME"]!=$_SESSION['CURRENT_LOCATION']['CURRENT']["DOMAIN"] && COption::GetOptionString("main","redirectdomain")=="Y")
				{
					//header("Location: https://".$_SESSION['CURRENT_LOCATION']['CURRENT']["DOMAIN"]); 
				}
				//Debug::dump(9); ЗДЕСЬ!
			} else {

				$_SESSION['CURRENT_LOCATION']['CURRENT']=$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT'];				
				$_SESSION['CURRENT_LOCATION']['CURRENT2']=$_SESSION['CURRENT_LOCATION']['LOCATIONS']['DEFAULT'];				
				$_SESSION['CURRENT_LOCATION']['CURRENT']['APPROVE']='Y';
				setcookie("CURRENT_LOCATION_ID",$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'],time()+86000,"/");	
			}
			
			$arrCityLocationFilter=array('LOGIC'=>'OR', 
							array('PROPERTY_CITY'=>false), 
							array('PROPERTY_CITY'=>$_SESSION['CURRENT_LOCATION']['CURRENT']['ID'])
			);		
			$_SESSION['CURRENT_LOCATION']['CURRENT']['FILTER']=$arrCityLocationFilter;
		}			
	}
}
//Debug::dump($arLocation, "arLocation:");		
if ($_SERVER["SERVER_NAME"]!="kamin-sklad.ru")
{
	foreach ($_SESSION['CURRENT_LOCATION']['LOCATIONS'] as $key => $arLocation){
		if ($_SERVER["SERVER_NAME"]==$arLocation["DOMAIN"]){
			$_SESSION['CURRENT_LOCATION']['CURRENT2']=$arLocation;
		}
	}
}	
	
if($_SESSION['CURRENT_LOCATION']['CURRENT']['PAGE']=="Y") {
	if($APPLICATION->GetCurDir()!="/") {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /");

		exit(); 	
	}
}
?>
