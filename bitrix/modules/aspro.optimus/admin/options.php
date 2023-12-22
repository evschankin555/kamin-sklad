<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);
$RIGHT = $APPLICATION->GetGroupRight(COptimus::moduleID);

if($RIGHT >= "R"){
	$res = COptimus::getModuleOptionsList();
	$arTabs = $res["TABS"];
	$tabControl = new CAdminTabControl("tabControl", $arTabs);

	if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT >= "W" && check_bitrix_sessid()){
		global $APPLICATION, $CACHE_MANAGER;

		if(strlen($RestoreDefaults) > 0){
			COption::RemoveOption(COptimus::moduleID);
			COption::RemoveOption(COptimus::moduleID, "NeedGenerateCustomTheme");
			COption::RemoveOption(COptimus::moduleID, "NeedGenerateCustomThemeBG");
			$APPLICATION->DelGroupRight(COptimus::moduleID);
		}
		else{
			COption::RemoveOption(COptimus::moduleID, "sid");
			foreach($arTabs as $key => $arTab){
				foreach($arTab["OPTIONS"] as $arOption){
					if($arOption[0] == "COLOR_THEME" && $_REQUEST[$arOption[0]."_".$arTab["SITE_ID"]] === 'CUSTOM'){
						COption::SetOptionString(COptimus::moduleID, "NeedGenerateCustomTheme", 'Y', '', $arTab["SITE_ID"]);
					}
					if($arOption[0] == "BGCOLOR_THEME" && $_REQUEST[$arOption[0]."_".$arTab["SITE_ID"]] === 'CUSTOM'){
						COption::SetOptionString(COptimus::moduleID, "NeedGenerateCustomThemeBG", 'Y', '', $arTab["SITE_ID"]);
					}
					$arOption[0] = $arOption[0]."_".$arTab["SITE_ID"];
					COptimus::__AdmSettingsSaveOption_EX(COptimus::moduleID, $arOption);
				}

				CBitrixComponent::clearComponentCache('bitrix:catalog.element', $arTab["SITE_ID"]);
				CBitrixComponent::clearComponentCache('bitrix:catalog.section', $arTab["SITE_ID"]);
				CBitrixComponent::clearComponentCache('bitrix:catalog.store.amount', $arTab["SITE_ID"]);
				CBitrixComponent::clearComponentCache('bitrix:menu', $arTab["SITE_ID"]);
			}
		}

		UnRegisterModuleDependences("main", "OnEndBufferContent", COptimus::moduleID, "COptimus", "InsertCounters");
		RegisterModuleDependences("main", "OnEndBufferContent", COptimus::moduleID, "COptimus", "InsertCounters");

		// clear composite cache
		$compositeMode = COptimus::IsCompositeEnabled();
		$obCache = new CPHPCache();
		$obCache->CleanDir("", "html_pages");
		if($compositeMode){
			COptimus::EnableComposite($compositeMode === 'AUTO_COMPOSITE');
		}

		$APPLICATION->RestartBuffer();
	}

	CJSCore::Init(array("jquery"));
	CAjax::Init();
	?>
	<?if(!count($arTabs)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage("NO_SITE_INSTALLED", array("#SESSION_ID#"=>bitrix_sessid_get()))?></div>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?else:?>
		<?$tabControl->Begin();?>
		<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>" class="optimus_options" ENCTYPE="multipart/form-data">
			<?=bitrix_sessid_post();?>
			<?
			CModule:: IncludeModule('sale');
			$arPersonTypes = $arDeliveryServices = $arPaySystems = $arCurrency = $arOrderPropertiesByPerson = $arS = $arC = $arN = array();
			$dbRes = CSalePersonType::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array());
			while($arItem = $dbRes->Fetch()){
				if($arItem['LIDS'] && is_array($arItem['LIDS'])){
					foreach($arItem['LIDS'] as $site_id){
						$arPersonTypes[$site_id][$arItem['ID']] = '['.$arItem['ID'].'] '.$arItem['NAME'].' ('.$site_id.')';
					}
				}
				$arS[$arItem['ID']] = array('FIO', 'PHONE', 'EMAIL');
				$arN[$arItem['ID']] = array(
					'FIO' => GetMessage('ONECLICKBUY_PROPERTIES_FIO'),
					'PHONE' => GetMessage('ONECLICKBUY_PROPERTIES_PHONE'),
					'EMAIL' => GetMessage('ONECLICKBUY_PROPERTIES_EMAIL'),
				);
			}

			foreach($arTabs as $key => $arTab){
				if($arTab["SITE_ID"]){
					$dbRes = CSaleDelivery::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y', 'LID' => $arTab["SITE_ID"]), false, false, array());
					while($arItem = $dbRes->Fetch()){
						$arDeliveryServices[$arTab["SITE_ID"]][$arItem['ID']] = '['.$arItem['ID'].'] '.$arItem['NAME'].' ('.$arTab["SITE_ID"].')';
					}
				}
			}

			$dbRes = CSalePaySystem::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array());
			while($arItem = $dbRes->Fetch()){
				$arPaySystems[$arItem['ID']] = '['.$arItem['ID'].'] '.$arItem['NAME'];
			}

			$dbRes = CCurrency::GetList(($by = "sort"), ($order = "asc"), LANGUAGE_ID);
			while($arItem = $dbRes->Fetch()){
				$arCurrency[$arItem['CURRENCY']] = $arItem['FULL_NAME'].' ('.$arItem['CURRENCY'].')';
			}

			$dbRes = CSaleOrderProps::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array('ID', 'CODE', 'NAME', 'PERSON_TYPE_ID', 'TYPE', 'IS_PHONE', 'IS_EMAIL', 'IS_PAYER'));
			while($arItem = $dbRes->Fetch()){
				if($arItem['TYPE'] === 'TEXT' && strlen($arItem['CODE'])){
					$arN[$arItem['PERSON_TYPE_ID']][$arItem['CODE']] = $arItem['NAME'];
					if($arItem['IS_PAYER'] === 'Y'){
						$arS[$arItem['PERSON_TYPE_ID']][0] = $arItem['CODE'];
					}
					elseif($arItem['IS_PHONE'] === 'Y'){
						$arS[$arItem['PERSON_TYPE_ID']][1] = $arItem['CODE'];
					}
					elseif($arItem['IS_EMAIL'] === 'Y'){
						$arS[$arItem['PERSON_TYPE_ID']][2] = $arItem['CODE'];
					}
					else{
						$arS[$arItem['PERSON_TYPE_ID']][] = $arItem['CODE'];
					}
				}
			}
			if($arS && $arN){
				foreach($arS as $PERSON_TYPE_ID => $arCodes){
					if($arCodes){
						foreach($arCodes as $CODE){
							$arOrderPropertiesByPerson[$PERSON_TYPE_ID][$CODE] = $arN[$PERSON_TYPE_ID][$CODE];
						}
						$arOrderPropertiesByPerson[$PERSON_TYPE_ID]['COMMENT'] = GetMessage('ONECLICKBUY_PROPERTIES_COMMENT');
					}
				}
			}

			foreach($arTabs as $key => $arTab){
				$tabControl->BeginNextTab();
				if($arTab["SITE_ID"]){
					foreach($arTab["OPTIONS"] as $arOption){
						if($arOption[0] === "ONECLICKBUY_PERSON_TYPE"){
							$arOption[3][1] = $arPersonTypes[$arTab["SITE_ID"]];
						}
						elseif($arOption[0] === "ONECLICKBUY_DELIVERY"){
							$arOption[3][1] = $arDeliveryServices[$arTab["SITE_ID"]];
						}
						elseif($arOption[0] === "ONECLICKBUY_PAYMENT"){
							$arOption[3][1] = $arPaySystems;
						}
						elseif($arOption[0] === "ONECLICKBUY_CURRENCY"){
							$arOption[3][1] = $arCurrency;
						}
						elseif($arOption[0] === "ONECLICKBUY_PROPERTIES" || $arOption[0] === "ONECLICKBUY_REQUIRED_PROPERTIES"){
							$arOption[3][1] = $arOrderPropertiesByPerson[COption::GetOptionString('aspro.optimus', 'ONECLIKBUY_PERSON_TYPE', ($arPersonTypes ? key($arPersonTypes[$arTab["SITE_ID"]]) : ''), $arTab["SITE_ID"])];
						}
						$arOption[0] = $arOption[0]."_".$arTab["SITE_ID"];
						COptimus::__AdmSettingsDrawRow_EX(COptimus::moduleID, $arOption, $arTab["SITE_ID"]);
					}
				}
			}
			if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && check_bitrix_sessid()){
				if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"]) > 0) LocalRedirect($_REQUEST["back_url_settings"]);
				else LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
			}
			?>
			<script>
			var arOrderPropertiesByPerson = <?=CUtil::PhpToJSObject($arOrderPropertiesByPerson, false)?>;

			function CheckActive(){
				$('input[name^="USE_WORD_EXPRESSION"]').each(function() {
					var input = this;
					var isActiveUseExpressions = $(input).attr('checked') == 'checked';
					var tab = $(input).parents('.adm-detail-content-item-block');
					if(!isActiveUseExpressions){
						tab.find('input[name^="MAX_AMOUNT"]').attr('disabled', 'disabled');
						tab.find('input[name^="MIN_AMOUNT"]').attr('disabled', 'disabled');
						tab.find('input[name^="EXPRESSION_FOR_MIN"]').attr('disabled', 'disabled');
						tab.find('input[name^="EXPRESSION_FOR_MAX"]').attr('disabled', 'disabled');
						tab.find('input[name^="EXPRESSION_FOR_MID"]').attr('disabled', 'disabled');
					}
					else{
						tab.find('input[name^="MAX_AMOUNT"]').removeAttr('disabled');
						tab.find('input[name^="MIN_AMOUNT"]').removeAttr('disabled');
						tab.find('input[name^="EXPRESSION_FOR_MIN"]').removeAttr('disabled');
						tab.find('input[name^="EXPRESSION_FOR_MAX"]').removeAttr('disabled');
						tab.find('input[name^="EXPRESSION_FOR_MID"]').removeAttr('disabled');
					}
				});

				$('select[name^="BUYMISSINGGOODS"]').each(function() {
					var select = this;
					var BuyMissingGoodsVal = $(select).val();
					var tab = $(select).parents('.adm-detail-content-item-block');
					tab.find('input[name^="EXPRESSION_SUBSCRIBE_BUTTON"]').attr('disabled', 'disabled');
					tab.find('input[name^="EXPRESSION_SUBSCRIBED_BUTTON"]').attr('disabled', 'disabled');
					tab.find('input[name^="EXPRESSION_ORDER_BUTTON"]').attr('disabled', 'disabled');
					if(BuyMissingGoodsVal == 'SUBSCRIBE'){
						tab.find('input[name^="EXPRESSION_SUBSCRIBE_BUTTON"]').removeAttr('disabled');
						tab.find('input[name^="EXPRESSION_SUBSCRIBED_BUTTON"]').removeAttr('disabled');
					}
					else if(BuyMissingGoodsVal == 'ORDER'){
						tab.find('input[name^="EXPRESSION_ORDER_BUTTON"]').removeAttr('disabled');
					}
				});
			}

			function checkGoalsNote(){
				var inUAC = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
				var itrYACID = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=YA_COUNTER_ID]');
				var itrGNote = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=GOALS_NOTE]');
				var itrUFG = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_FORMS_GOALS]');
				var itrUBG = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_BASKET_GOALS]');
				var itrU1CG = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_1CLICK_GOALS]');
				var itrUQOG = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_FASTORDER_GOALS]');
				var itrUFOG = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_FULLORDER_GOALS]');
				var itrUDG = $('.adm-detail-content-table:visible').first().find('tr[data-optioncode=USE_DEBUG_GOALS]');

				if(inUAC.length && inUAC.attr('checked')){
					var bShowNote = 6;

					if(itrUFG.find('select').val().indexOf('NONE') == -1){
						itrGNote.find('[data-goal=form]').show();
					}
					else{
						itrGNote.find('[data-goal=form]').hide();
						--bShowNote;
					}

					if(itrUBG.find('input').attr('checked')){
						itrGNote.find('[data-goal=basket]').show();
					}
					else{
						itrGNote.find('[data-goal=basket]').hide();
						--bShowNote;
					}

					if(itrU1CG.find('input').attr('checked')){
						itrGNote.find('[data-goal=1click]').show();
					}
					else{
						itrGNote.find('[data-goal=1click]').hide();
						--bShowNote;
					}

					if(itrUQOG.find('input').attr('checked')){
						itrGNote.find('[data-goal=fastorder]').show();
					}
					else{
						itrGNote.find('[data-goal=fastorder]').hide();
						--bShowNote;
					}

					if(itrUFOG.find('input').attr('checked')){
						itrGNote.find('[data-goal=fullorder]').show();
					}
					else{
						itrGNote.find('[data-goal=fullorder]').hide();
						--bShowNote;
					}

					if(itrUDG.find('input').attr('checked')){
						itrGNote.find('[data-goal=debug]').show();
					}
					else{
						itrGNote.find('[data-goal=debug]').hide();
						--bShowNote;
					}

					if(bShowNote){
						itrGNote.fadeIn();
					}
					else{
						itrGNote.fadeOut();
					}
				}
				else{
					itrGNote.fadeOut();
				}
			}

			$(document).ready(function() {
				CheckActive();

				$('form.optimus_options').submit(function(e) {
					$(this).attr('id', 'optimus_options');
					jsAjaxUtil.ShowLocalWaitWindow('id', 'optimus_options', true);
					$(this).find('input').removeAttr('disabled');
				});

				$('input[name^="USE_WORD_EXPRESSION"], select[name^="BUYMISSINGGOODS"]').change(function() {
					CheckActive();
				});

				$('select[name^="SHOW_SECTION_DESCRIPTION"]').change(function(){
					console.log($('select[name^="SECTION_DESCRIPTION"]'));
					if($(this).val() != 'BOTH')
						$('select[name*="SECTION_DESCRIPTION_POSITION"]').closest('tr').css('display','none');
					else
						$('select[name*="SECTION_DESCRIPTION_POSITION"]').closest('tr').css('display','');
				});

				$('select[name^="SHOW_QUANTITY_FOR_GROUPS"]').change(function() {
					var val = $(this).val();
					var tab = $(this).parents('.adm-detail-content-item-block');
					var sqcg = tab.find('select[name^="SHOW_QUANTITY_COUNT_FOR_GROUPS"]');

					var isAll = false;
					if(val){
						isAll = val.indexOf('2') !== -1;
					}

					if(!isAll){
						$(this).find('option').each(function() {
							if($(this).attr('selected') != 'selected'){
								sqcg.find('option[value="' + $(this).attr('value') + '"]').removeAttr('selected');
							}
						});
					}
				});

				$('select[name^="SHOW_QUANTITY_COUNT_FOR_GROUPS"]').change(function(e) {
					e.stopPropagation();
					var val = $(this).val();
					var tab = $(this).parents('.adm-detail-content-item-block');
					var sqg_val = tab.find('select[name^="SHOW_QUANTITY_FOR_GROUPS"]').val();

					if(!sqg_val){
						$(this).find('option').removeAttr('selected');
						return;
					}

					var isAll = false;
					if(sqg_val){
						isAll = sqg_val.indexOf('2') !== -1;
					}

					if(!isAll && val){
						for(i in val){
							var g = val[i];
							if(sqg_val.indexOf(g) === -1){
								$(this).find('option[value="' + g + '"]').removeAttr('selected');
							}
						}
					}
				});

				$('select[name^="ONECLICKBUY_PERSON_TYPE"]').change(function() {
					if(typeof arOrderPropertiesByPerson !== 'undefined'){
						var table = $(this).parents('table').first();
						var value = $(this).val();
						if(typeof value !== 'undefined' && typeof arOrderPropertiesByPerson[value] !== 'undefined'){
							var arSelects = [table.find('select[name^=ONECLICKBUY_PROPERTIES]'), table.find('select[name^=ONECLICKBUY_REQUIRED_PROPERTIES]')];
							for(var i in arSelects){
								var $fields = arSelects[i];
								if($fields.length){
									var fields = $fields.val();
									$fields.find('option').remove();
									for(var j in arOrderPropertiesByPerson[value]){
										var selected = fields.indexOf(j) !== -1 ? ' selected="selected"' : '';
										$fields.append('<option value="' + j + '"' + selected + '>' + arOrderPropertiesByPerson[value][j] + '</option>');
									}
									$fields.find('option').eq(0).attr('selected', 'selected');
									$fields.find('option').eq(1).attr('selected', 'selected');
								}
							}
						}
					}
				});

				$('select[name^="ONECLICKBUY_PROPERTIES"]').change(function() {
					var table = $(this).parents('table').first();
					$(this).find('option').eq(0).attr('selected', 'selected');
					$(this).find('option').eq(1).attr('selected', 'selected');
					var fiedsValue = $(this).val();
					var $requiredFields = table.find('select[name^=ONECLICKBUY_REQUIRED_PROPERTIES]');
					var requiredFieldsValue = $requiredFields.val();
					for(var i in requiredFieldsValue){
						if(fiedsValue === null || fiedsValue.indexOf(requiredFieldsValue[i]) === -1){
							$requiredFields.find('option[value=' + requiredFieldsValue[i] + ']').removeAttr('selected');
						}
					}
				});

				$('select[name^="ONECLICKBUY_REQUIRED_PROPERTIES"]').change(function() {
					var table = $(this).parents('table').first();
					$(this).find('option').eq(0).attr('selected', 'selected');
					$(this).find('option').eq(1).attr('selected', 'selected');
					var requiredFieldsValue = $(this).val();
					var $fieds = table.find('select[name^=ONECLICKBUY_PROPERTIES]');
					var fiedsValue = $fieds.val();
					var $FIO = $(this).find('option[value^=FIO]');
					var $PHONE = $(this).find('option[value^=PHONE]');
					for(var i in requiredFieldsValue){
						if(fiedsValue === null || fiedsValue.indexOf(requiredFieldsValue[i]) === -1){
							$(this).find('option[value=' + requiredFieldsValue[i] + ']').removeAttr('selected');
						}
					}
				});

				$('select[name^="SCROLLTOTOP_TYPE"]').change(function() {
					var posSelect = $(this).parents('table').first().find('select[name^="SCROLLTOTOP_POSITION"]');
					if(posSelect){
						var posSelectTr = posSelect.parents('tr').first();
						var isNone = $(this).val().indexOf('NONE') != -1;
						if(isNone){
							if(posSelectTr.is(':visible')){
								posSelectTr.fadeOut();
							}
						}
						else{
							if(!posSelectTr.is(':visible')){
								posSelectTr.fadeIn();
							}
							var isRound = $(this).val().indexOf('ROUND') != -1;
							var isTouch = posSelect.val().indexOf('TOUCH') != -1;
							if(isRound && !!posSelect){
								posSelect.find('option[value^="TOUCH"]').attr('disabled', 'disabled');
								if(isTouch){
									posSelect.val(posSelect.find('option[value^="PADDING"]').first().attr('value'));
								}
							}
							else{
								posSelect.find('option[value^="TOUCH"]').removeAttr('disabled');
							}
						}
					}
				});

				$('input[name^="USE_YA_COUNTER"]').change(function() {
					var itrYCC = $(this).parents('table').first().find('tr[data-optioncode=YANDEX_COUNTER]');
					var itrYACID = $(this).parents('table').first().find('tr[data-optioncode=YA_COUNTER_ID]');
					var itrYE = $(this).parents('table').first().find('tr[data-optioncode=YANDEX_ECOMERCE]');
					var itrUFG = $(this).parents('table').first().find('tr[data-optioncode=USE_FORMS_GOALS]');
					var itrUBG = $(this).parents('table').first().find('tr[data-optioncode=USE_BASKET_GOALS]');
					var itrU1CG = $(this).parents('table').first().find('tr[data-optioncode=USE_1CLICK_GOALS]');
					var itrUQOG = $(this).parents('table').first().find('tr[data-optioncode=USE_FASTORDER_GOALS]');
					var itrUFOG = $(this).parents('table').first().find('tr[data-optioncode=USE_FULLORDER_GOALS]');
					var itrUDG = $(this).parents('table').first().find('tr[data-optioncode=USE_DEBUG_GOALS]');
					var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
					var ischecked = $(this).attr('checked');
					if(typeof(ischecked) != 'undefined'){
						itrYCC.fadeIn();
						itrYACID.fadeIn();
						itrYE.fadeIn();
						itrUFG.fadeIn();
						var valUFG = itrUFG.find('select').val();
						if(valUFG.indexOf('NONE') == -1){
							var isCommon = valUFG.indexOf('COMMON') != -1;
							if(isCommon){
								itrGNote.find('[data-value=common]').show();
								itrGNote.find('[data-value=single]').hide();
							}
							else{
								itrGNote.find('[data-value=common]').hide();
								itrGNote.find('[data-value=single]').show();
							}
						}
						itrUBG.fadeIn();
						itrU1CG.fadeIn();
						itrUQOG.fadeIn();
						itrUFOG.fadeIn();
						itrUDG.fadeIn();
					}
					else{
						itrYCC.fadeOut();
						itrYACID.fadeOut();
						itrYE.fadeOut();
						itrUFG.fadeOut();
						itrUBG.fadeOut();
						itrU1CG.fadeOut();
						itrUQOG.fadeOut();
						itrUFOG.fadeOut();
						itrUDG.fadeOut();
						itrGNote.fadeOut();
					}

					checkGoalsNote();
				});

				$('select[name^="USE_FORMS_GOALS"]').change(function() {
					var inUAC = $(this).parents('table').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
					if(inUAC.length && inUAC.attr('checked')){
						var isNone = $(this).val().indexOf('NONE') != -1;
						var isCommon = $(this).val().indexOf('COMMON') != -1;
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						if(!isNone){
							if(isCommon){
								itrGNote.find('[data-value=common]').show();
								itrGNote.find('[data-value=single]').hide();
							}
							else{
								itrGNote.find('[data-value=common]').hide();
								itrGNote.find('[data-value=single]').show();
							}
							itrGNote.find('[data-goal=form]').show();
						}
						else{
							itrGNote.find('[data-goal=form]').hide();
						}
					}

					checkGoalsNote();
				});

				$('input[name^="USE_BASKET_GOALS"]').change(function() {
					var inUAC = $(this).parents('table').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=basket]').show();
						}
						else{
							itrGNote.find('[data-goal=basket]').hide();
						}
					}

					checkGoalsNote();
				});

				$('input[name^="USE_1CLICK_GOALS"]').change(function() {
					var inUAC = $(this).parents('table').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=1click]').show();
						}
						else{
							itrGNote.find('[data-goal=1click]').hide();
						}
					}

					checkGoalsNote();
				});

				$('input[name^="USE_FASTORDER_GOALS"]').change(function() {
					var inUAC = $(this).parents('table').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=fastorder]').show();
						}
						else{
							itrGNote.find('[data-goal=fastorder]').hide();
						}
					}

					checkGoalsNote();
				});

				$('input[name^="USE_FULLORDER_GOALS"]').change(function() {
					var inUAC = $(this).parents('table').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=fullorder]').show();
						}
						else{
							itrGNote.find('[data-goal=fullorder]').hide();
						}
					}

					checkGoalsNote();
				});

				$('input[name^="USE_DEBUG_GOALS"]').change(function() {
					var inUAC = $(this).parents('table').first().find('tr[data-optioncode=USE_YA_COUNTER] input');
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=debug]').show();
						}
						else{
							itrGNote.find('[data-goal=debug]').hide();
						}
					}

					checkGoalsNote();
				});

				$('select[name^="SCROLLTOTOP_TYPE"]').change();
				$('select[name^="ONECLICKBUY_PERSON_TYPE"]').change();
				$('input[name^="USE_YA_COUNTER"]').change();
				$('select[name^="USE_FORMS_GOALS"]').change();
				$('input[name^="USE_BASKET_GOALS"]').change();
				$('input[name^="USE_1CLICK_GOALS"]').change();
				$('input[name^="USE_FASTORDER_GOALS"]').change();
				$('input[name^="USE_FULLORDER_GOALS"]').change();
				$('input[name^="USE_DEBUG_GOALS"]').change();
			});
			</script>
			<?$tabControl->Buttons();?>
			<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
			<?if(strlen($_REQUEST["back_url_settings"]) > 0): ?>
				<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
			<?endif;?>
			<?if(COptimus::IsCompositeEnabled()):?>
				<div class="adm-info-message"><?=GetMessage("WILL_CLEAR_HTML_CACHE_NOTE")?></div><div style="clear:both;"></div>
				<script type="text/javascript">
				$(document).ready(function() {
					$('input[name^="THEME_SWITCHER"]').change(function() {
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							if(!confirm("<?=GetMessage("NO_COMPOSITE_NOTE")?>")){
								$(this).removeAttr('checked');
							}
						}
					});
				});
				</script>
			<?endif;?>
		</form>
		<?$tabControl->End();?>
	<?endif;?>
	<?
}
else{
	echo CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));
}
?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>