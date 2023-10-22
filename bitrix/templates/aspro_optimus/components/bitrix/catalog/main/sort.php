<?$arDisplays = array("block", "list", "table");
if(array_key_exists("display", $_REQUEST) || (array_key_exists("display", $_SESSION)) || $arParams["DEFAULT_LIST_TEMPLATE"]){
	if($_REQUEST["display"] && (in_array(trim($_REQUEST["display"]), $arDisplays))){
		$display = trim($_REQUEST["display"]);
		$_SESSION["display"]=trim($_REQUEST["display"]);
	}
	elseif($_SESSION["display"] && (in_array(trim($_SESSION["display"]), $arDisplays))){
		$display = $_SESSION["display"];
	}
	elseif($arSection["DISPLAY"]){
		$display = $arSection["DISPLAY"];
	}
	else{
		$display = $arParams["DEFAULT_LIST_TEMPLATE"];
	}
}
else{
	$display = "block";
}
$template = "catalog_".$display;
?>
<a name="goods"></a>
<div class="sort_header view_<?=$display?>">
	<!--noindex-->
		<div class="sort_filter">
			<?	
			$arAvailableSort = array();
			$arSorts = $arParams["SORT_BUTTONS"];
			if(in_array("POPULARITY", $arSorts)){
				$arAvailableSort["SHOWS"] = array("SHOWS", "desc");
			}
			if(in_array("NAME", $arSorts)){
				$arAvailableSort["NAME"] = array("NAME", "asc");
			}
			
			if(in_array("PRICE", $arSorts)){ 
				$arSortPrices = $arParams["SORT_PRICES"];
				if(strpos($arSortPrices,"MINIMUM_PRICE")!==false || strpos($arSortPrices,"MAXIMUM_PRICE")!==false){
					$arAvailableSort["PRICE"] = array("PROPERTY_".$arSortPrices, "desc");
				}
				else{
					$price = CCatalogGroup::GetList(array(), array("NAME" => $arParams["SORT_PRICES"]), false, false, array("ID", "NAME"))->GetNext();
					$arAvailableSort["PRICE"] = array("CATALOG_PRICE_".$price["ID"], "desc"); 
				}
			}
			if(in_array("QUANTITY", $arSorts)){
				$arAvailableSort["CATALOG_AVAILABLE"] = array("QUANTITY", "desc");
			}
			$sort = "PRICE";
			if((array_key_exists("sort", $_REQUEST) && array_key_exists(ToUpper($_REQUEST["sort"]), $arAvailableSort)) || (array_key_exists("sort", $_SESSION) && array_key_exists(ToUpper($_SESSION["sort"]), $arAvailableSort)) || $arParams["ELEMENT_SORT_FIELD"]){
				if($_REQUEST["sort"]){
					$sort = ToUpper($_REQUEST["sort"]); 
					$_SESSION["sort"] = ToUpper($_REQUEST["sort"]);
				}
				elseif($_SESSION["sort"]){
					$sort = ToUpper($_SESSION["sort"]);
				}
				else{
					$sort = ToUpper($arParams["ELEMENT_SORT_FIELD"]);
				}
			}

			$sort_order=$arAvailableSort[$sort][1];
			if((array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc"))) || (array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ) || $arParams["ELEMENT_SORT_ORDER"]){
				if($_REQUEST["order"]){
					$sort_order = $_REQUEST["order"];
					$_SESSION["order"] = $_REQUEST["order"];
				}
				elseif($_SESSION["order"]){
					$sort_order = $_SESSION["order"];
				}
				else{
					$sort_order = ToLower($arParams["ELEMENT_SORT_ORDER"]);
				}
			}
			?>
			<?foreach($arAvailableSort as $key => $val):?>
				<?$newSort = $sort_order == 'desc' ? 'asc' : 'desc';
				$current_url = $APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'));
				$url = str_replace('+', '%2B', $current_url);?>
				<a href="<?=$url;?>#goods" class="sort_btn <?=($sort == $key ? 'current' : '')?> <?=$sort_order?> <?=$key?>" rel="nofollow">
					<i class="icon" title="<?=GetMessage('SECT_SORT_'.$key)?>"></i><span><?=GetMessage('SECT_SORT_'.$key)?></span><i class="arr icons_fa"></i>
				</a>
			<?endforeach;?>
                  
            <span style="position: absolute;margin-top: -12px;">                    
			<span style="font-size: 12px;line-height: 13px;color: #a5a3a3;padding-top: 12px;display: inline-block; margin-right: 5px;">Выводить по: &nbsp; </span>
            <span style=" width: 60px; display: inline-block;margin-top: 5px;position: absolute;">
            <select onChange="location.href=this.value;">
				<?
                    $current_url = str_replace('+', '%2B', $APPLICATION->GetCurPageParam('size=12',array('size')));
                    echo("<option ".(($arParams["PAGE_ELEMENT_COUNT"]=="12")?"selected":"")." value=\"".$current_url."#goods\">12</option>");
                    $current_url = str_replace('+', '%2B', $APPLICATION->GetCurPageParam('size=24',array('size')));
                    echo("<option ".(($arParams["PAGE_ELEMENT_COUNT"]=="24")?"selected":"")." value=\"".$current_url."#goods\">24</option>");
                    $current_url = str_replace('+', '%2B', $APPLICATION->GetCurPageParam('size=48',array('size')));
                    echo("<option ".(($arParams["PAGE_ELEMENT_COUNT"]=="48")?"selected":"")." value=\"".$current_url."#goods\">48</option>");
                    $current_url = str_replace('+', '%2B', $APPLICATION->GetCurPageParam('size=72',array('size')));
                    echo("<option ".(($arParams["PAGE_ELEMENT_COUNT"]=="72")?"selected":"")." value=\"".$current_url."#goods\">72</option>");
                ?>
            </select>
            </span>
            </span>
            
			<?
			if($sort == "PRICE"){
				$sort = $arAvailableSort["PRICE"][0];
			}
			if($sort == "CATALOG_AVAILABLE"){
				$sort = "CATALOG_QUANTITY";
			}
			?>
		</div>
		<div class="sort_display">	
			<?foreach($arDisplays as $displayType):?>
				<?
				$current_url = '';
				$current_url = $APPLICATION->GetCurPageParam('display='.$displayType, 	array('display'));
				$url = str_replace('+', '%2B', $current_url);
				?>
				<a rel="nofollow" href="<?=$url;?>#goods" class="sort_btn <?=$displayType?> <?=($display == $displayType ? 'current' : '')?>"><i title="<?=GetMessage("SECT_DISPLAY_".strtoupper($displayType))?>"></i></a>
			<?endforeach;?>
		</div>
	<!--/noindex-->
</div>

<?
if(strpos($sort,"MINIMUM_PRICE")!==false){
	if ($sort_order=="asc") $sort_order="asc,nulls";
}
?>