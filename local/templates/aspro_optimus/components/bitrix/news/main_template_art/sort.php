			<?	
			$arParams["SORT_BY1"]="date";
			$arAvailableSort = array();
			$arAvailableSort["DATE"] = array("ACTIVE_FROM", "desc");
			$arAvailableSort["RATING"] = array("PROPERTY_rating", "desc");

			$sort = "date";
			if((array_key_exists("sort", $_REQUEST) && array_key_exists(ToUpper($_REQUEST["sort"]), $arAvailableSort)) || (array_key_exists("sort", $_SESSION) && array_key_exists(ToUpper($_SESSION["sort"]), $arAvailableSort)) || $arParams["SORT_BY1"]){
				if($_REQUEST["sort"]){
					$sort = ToUpper($_REQUEST["sort"]); 
					$_SESSION["sort"] = ToUpper($_REQUEST["sort"]);
				}
				elseif($_SESSION["sort"]){
					$sort = ToUpper($_SESSION["sort"]);
				}
				else{
					$sort = ToUpper($arParams["SORT_BY1"]);
				}
			}

			$sort_order=$arAvailableSort[$sort][1];
			if((array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc"))) || (array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ) || $arParams["SORT_ORDER1"]){
				if($_REQUEST["order"]){
					$sort_order = $_REQUEST["order"];
					$_SESSION["order"] = $_REQUEST["order"];
				}
				elseif($_SESSION["order"]){
					$sort_order = $_SESSION["order"];
				}
				else{
					$sort_order = ToLower($arParams["SORT_ORDER1"]);
				}
			}
			?>

<?if($arParams["AJAX_REQUEST"]!="Y"){?>            
<div class="sort_header view_<?=$display?>">
	<!--noindex-->
		<div class="sort_filter">
			<?foreach($arAvailableSort as $key => $val):?>
				<?$newSort = $sort_order == 'desc' ? 'asc' : 'desc';
				$current_url = $APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'));
				$url = str_replace('+', '%2B', $current_url);?>
				<a href="<?=$url;?>" class="sort_btn <?=($sort == $key ? 'current' : '')?> <?=$sort_order?> <?=$key?>" rel="nofollow">
					<i class="icon" title="<?=GetMessage('SECT_SORT_'.$key)?>"></i><span style="display:inline-block !important;"><?=GetMessage('SECT_SORT_'.$key)?></span><i class="arr icons_fa"></i>
				</a>
			<?endforeach;?>            
		</div>
	<!--/noindex-->
</div>
<? } ?>