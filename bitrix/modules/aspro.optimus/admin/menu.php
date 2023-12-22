<?
AddEventHandler('main', 'OnBuildGlobalMenu', 'OnBuildGlobalMenuHandlerOptimus');
function OnBuildGlobalMenuHandlerOptimus(&$arGlobalMenu, &$arModuleMenu){
	if(!defined('OPTIMUS_MENU_INCLUDED')){
		define('OPTIMUS_MENU_INCLUDED', true);

		IncludeModuleLangFile(__FILE__);
		$moduleID = 'aspro.optimus';
		
		$GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$moduleID."/menu.css");

		if($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R'){
			$arMenu = array(
				'menu_id' => 'global_menu_aspro_optimus',
				'text' => GetMessage('OPTIMUS_GLOBAL_MENU_TEXT'),
				'title' => GetMessage('OPTIMUS_GLOBAL_MENU_TITLE'),
				'sort' => 1000,
				'items_id' => 'global_menu_aspro_optimus_items',
				'items' => array(
					array(
						'text' => GetMessage('OPTIMUS_MENU_CONTROL_CENTER_TEXT'),
						'title' => GetMessage('OPTIMUS_MENU_CONTROL_CENTER_TITLE'),
						'sort' => 10,
						'url' => '/bitrix/admin/'.$moduleID.'_mc.php',
						'icon' => 'imi_control_center',
						'page_icon' => 'pi_control_center',
						'items_id' => 'control_center',
					),
					array(
						'text' => GetMessage('OPTIMUS_MENU_TYPOGRAPHY_TEXT'),
						'title' => GetMessage('OPTIMUS_MENU_TYPOGRAPHY_TITLE'),
						'sort' => 20,
						'url' => '/bitrix/admin/'.$moduleID.'_options.php?mid=main',
						'icon' => 'imi_typography',
						'page_icon' => 'pi_typography',
						'items_id' => 'main',
					),					
				),
			);

			$arGlobalMenu[] = $arMenu;
		}
	}
}
?>