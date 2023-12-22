<?
#################################################
#        Company developer: IPOL
#        Developer: Egorov Nikita
#        Site: http://www.ipolh.com
#        E-mail: om-sv2@mail.ru
#        Copyright (c) 2006-2012 IPOL
#################################################
?>
<?
use Ipolh\MO\Tools;

CJSCore::Init(array("jquery"));
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

CModule::IncludeModule("ipol.mailorder");
CModule::IncludeModule('sale');

$module_id = IPOLH_MO;
$LABEL     = IPOLH_MO_LBL;

$strWarning = "";

$arAllOptions = Ipolh\MO\option::toOptions();

$arTabs = array(
	array(
		"DIV"   => "edit1",
		"TAB"   => Ipolh\MO\Tools::getMessage("FAQ"),
		"TITLE" => Ipolh\MO\Tools::getMessage("FAQ_TITLE"),
		"PATH"  => Tools::defaultOptionPath() . "FAQ.php"
	),
	array(
		"DIV"   => "edit2",
		"TAB"   => GetMessage("MAIN_TAB_SET"),
		"TITLE" => GetMessage("MAIN_TAB_TITLE_SET"),
		"PATH"  => Tools::defaultOptionPath() . "setups.php"
	),
	array(
		"DIV"   => "edit3",
		"TAB"   => Ipolh\MO\Tools::getMessage("ADDSETUPS"),
		"TITLE" => Ipolh\MO\Tools::getMessage("ADDSETUPS_TITLE"),
		"PATH"  => Tools::defaultOptionPath() . "additional.php"
	),
);

$_arTabs = array();
foreach(GetModuleEvents($module_id,"onTabsBuild",true) as $arEvent){
	ExecuteModuleEventEx($arEvent,Array(&$_arTabs));
}

$divId = count($arTabs);
if(!empty($_arTabs)){
	foreach($_arTabs as $tabName => $path){
		$arTabs[]=array(
			"DIV"   => "edit".(++$divId),
			"TAB"   => $tabName,
			"TITLE" => $tabName,
			"PATH"  => $path);
	}
}


if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid()){
    COption::RemoveOption($module_id);
}

$tabControl = new CAdminTabControl("tabControl", $arTabs);

function ShowParamsHTMLByArray($arParams)
{
	global $module_id;
	global $LABEL;
	foreach($arParams as $Option)
	{
		switch($Option[0])
		{
			case 'IPOLMO_OPT_WORKMODE': //режим работы
				$checked=array('','','');
				$checked[Ipolh\MO\option::get($Option[0])]='checked';
				Ipolh\MO\Tools::placeOptionRow(
					$Option[1].' <a href="javascript:void(0)" class="'.$LABEL.'PropHint" onclick="return '.$LABEL.'setups.popup(\'pop-WORKMODE\', this);"></a> :',
					'<input type="radio" value="1" name="'.$Option[0].'" id="IPOLMO_OPT_WORKMODE_1" '.$checked[1].'><label for="IPOLMO_OPT_WORKMODE_1">'.Ipolh\MO\Tools::getMessage("OPT_WORKMODE_1").'</label><br>
					<input type="radio" value="2" name="'.$Option[0].'" id="IPOLMO_OPT_WORKMODE_2" '.$checked[2].'><label for="IPOLMO_OPT_WORKMODE_2">'.Ipolh\MO\Tools::getMessage("OPT_WORKMODE_2").'</label>'
				);
					break;			
			case 'IPOLMO_OPT_TEXTMODE': //text/html
				$checked=array('','','');
				$checked[Ipolh\MO\option::get($Option[0])]='checked';
				Ipolh\MO\Tools::placeOptionRow(
					$Option[1],
					'<input type="radio" value="1" name="IPOLMO_OPT_TEXTMODE" id="IPOLMO_OPT_TEXTMODE_1" '.$checked[1].'><label for="IPOLMO_OPT_TEXTMODE_1">'.Ipolh\MO\Tools::getMessage("OPT_TEXTMODE_1").'</label><br>
					<input type="radio" value="2" name="IPOLMO_OPT_TEXTMODE" id="IPOLMO_OPT_TEXTMODE_2" '.$checked[2].'><label for="IPOLMO_OPT_TEXTMODE_2">HTML</label>'
				);
					break;		
			case 'IPOLMO_OPT_PROPS': //таблица со свойствами
				$savedPropsTmp=explode('|',Ipolh\MO\option::get($Option[0]));
				$savedProps=array();
				foreach($savedPropsTmp as $propGroup)
					if($propGroup)
						$savedProps[substr($propGroup,0,strpos($propGroup,'{'))]=','.substr($propGroup,strpos($propGroup,'{')+1,strpos($propGroup,'}')-strpos($propGroup,'{')-1);
				$orderProps=array();
				$arSpecNames=array('PERSON_TYPES'=>array(),'PROPS_GROUPS'=>array());
				$allProps=CSaleOrderProps::GetList();
				while($prop=$allProps->Fetch())
				{
					if(!array_key_exists($prop['PERSON_TYPE_ID'],$arSpecNames['PERSON_TYPES']))
						$arSpecNames['PERSON_TYPES'][$prop['PERSON_TYPE_ID']]=CSalePersonType::GetByID($prop['PERSON_TYPE_ID']);
					if(!array_key_exists($prop['PROPS_GROUP_ID'],$arSpecNames['PROPS_GROUPS']))
						$arSpecNames['PROPS_GROUPS'][$prop['PROPS_GROUP_ID']]=CSaleOrderPropsGroup::GetByID($prop['PROPS_GROUP_ID']);
					$orderProps[$prop['PERSON_TYPE_ID']][$prop['PROPS_GROUP_ID']][$prop['ID']]=array('NAME'=>$prop['NAME'],'CODE'=>$prop['CODE']);
				}
				$tableHead="<tr><td style='text-align:center;'>".Ipolh\MO\Tools::getMessage('OPT_PROPS_TABLE_NAME')."</td><td style='text-align:center;'>".Ipolh\MO\Tools::getMessage('OPT_PROPS_TABLE_CODE')."</td></tr>";
				echo '<tr class="heading">
						<td colspan="2" valign="top" align="center">'.Ipolh\MO\Tools::getMessage('OPT_PROPS').' <a href="javascript:void(0)" class="'.$LABEL.'PropHint" onclick="return '.$LABEL.'setups.popup(\'pop-MARKPROPS\', this);"></a></td>
					</tr>
					';
				//доставка
				echo '<tr class="propsPayer" id="IPOLMO_payer_0" onclick="'.$LABEL.'setups.getPage(\'props\').payerClick(0)">
						<td colspan="2" valign="top" align="center" >'.Ipolh\MO\Tools::getMessage('OPT_PROPS_COMMON').'</td>
					</tr>
					<tr><td colspan="2"><table style="width:100%" id="payer_0">'.$tableHead;
					
				foreach(array(array('CODE'=>'IMOPRICE','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_IMOPRICE')),array('CODE'=>'IMODELIVERY','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_DELIVERY')),array('CODE'=>'IMODELIVERYPRICE','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_DELIVERYPRC')),array('CODE' => 'IMOTRACKING','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_TRACKING')),array('CODE'=>'IMOPAYSYSTEM','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_PAYSYSTEM')),array('CODE'=>'IMOPAYED','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_IMOPAYED')),array('CODE'=>'IMOCOMMENT','NAME'=>Ipolh\MO\Tools::getMessage('OPT_PROPS_IMOCOMMENT'))) as $prop)
				{
					$marked='';
					if(strpos($savedProps[0],','.$prop['CODE'].' (')!==false) $marked='chosenTr';
					echo '<tr class="propsTable '.$marked.'" onclick="'.$LABEL.'setups.getPage(\'props\').trClick($(this))"><td>'.$prop['NAME'].'</td><td class="codeIsHere">'.$prop['CODE'].' ( #IPOLMO_'.$prop['CODE'].'# )</td></tr>';
				}
				echo '</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
				foreach($orderProps as $payerId => $payerProps)
				{
					echo '<tr class="propsPayer" id="IPOLMO_payer_'.$payerId.'" onclick="'.$LABEL.'setups.getPage(\'props\').payerClick('.$payerId.')">
						<td colspan="2" valign="top" align="center" >'.$arSpecNames['PERSON_TYPES'][$payerId]['NAME'].'</td>
					</tr>';
					echo '<tr><td colspan="2"><table style="width:100%" id="payer_'.$payerId.'">';
						foreach($payerProps as $groupId => $gropupProps)
						{
							echo '<tr class="propsGroup" onclick="'.$LABEL.'setups.getPage(\'props\').groupClick('.$groupId.','.$payerId.')">
								<td colspan="2" valign="top" align="center">'.$arSpecNames['PROPS_GROUPS'][$groupId]['NAME'].'</td>
							</tr><tr><td colspan="2"><table style="width:100%" id="group_'.$groupId.'">'.$tableHead;
							foreach($gropupProps as $propId => $prop)
							{
								$marked='';
								if(strpos($savedProps[$payerId],','.$prop['CODE'].' (')!==false) $marked='chosenTr';
								echo '<tr class="propsTable '.$marked.'" onclick="'.$LABEL.'setups.getPage(\'props\').trClick($(this))"><td>'.$prop['NAME'].'</td><td class="codeIsHere">'.$prop['CODE'].' ( #IPOLMO_'.$prop['CODE'].'# )</td></tr>';
							}
							echo '</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
						}
					echo '</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';
				}
				echo "<input type='hidden' name='IPOLMO_OPT_PROPS' value='".COption::GetOptionString($module_id,'IPOLMO_OPT_PROPS','')."'>";
				break;
			case "IPOLMO_OPT_EVENTS":
				echo "<tr class='heading'><td colspan='2' valign='top' align='center'>".Ipolh\MO\Tools::getMessage('OPT_EVENTS')."</td></tr>";
				$arEvents = array(
					"SALE_NEW_ORDER",
					"SALE_NEW_ORDER_RECURRING",
					"SALE_ORDER_CANCEL",
					"SALE_ORDER_DELIVERY",
					"SALE_ORDER_PAID",
					"SALE_ORDER_REMIND_PAYMENT",
					"SALE_RECURRING_CANCEL",
					"SALE_STATUS_CHANGED",
					"SALE_SUBSCRIBE_PRODUCT"
				);
				$checkedAr = explode(',',Ipolh\MO\option::get($Option[0]));
				foreach($arEvents as $event){
					$checked='';
					if(in_array($event,$checkedAr))
						$checked='checked';
					if($event == 'SALE_STATUS_CHANGED')
						$link = "<a target='_blank' href='/bitrix/admin/type_admin.php?PAGEN_1=1&SIZEN_1=75&lang=ru&set_filter=Y&find=SALE_STATUS_CHANGED&find_type=event_name&by=event_name&order=asc'>".Ipolh\MO\Tools::getMessage("OPT_EVENTS_TEMPLATE")."</a>";
					else
						$link = "<a target='_blank' href='/bitrix/admin/type_edit.php?EVENT_NAME=".$event."'>".Ipolh\MO\Tools::getMessage("OPT_EVENTS_TEMPLATE")."</a>";

					echo "<tr><td><label for='IPOLMO_CHECK_$event'>".Ipolh\MO\Tools::getMessage("OPT_EVENTS_".$event)."</td><td><input id='IPOLMO_CHECK_$event' type='checkbox' $checked name='IPOLMO_OPT_EVENTS[]' value='$event'>&nbsp;".$link."</td></tr>";
				}
				break;
			case "IPOLMO_OPT_ADDEVENTS":
				echo "<tr class='heading'><td colspan='2' valign='top' align='center'>".Ipolh\MO\Tools::getMessage('OPT_ADDEVENTS')."</td></tr>";
				$svd = unserialize(Ipolh\MO\option::get($Option[0]));
				foreach($svd as $rifle)
					echo "<tr><td colspan='2' style='text-align:center;'><input type='text' name='IPOLMO_OPT_ADDEVENTS[]' value='".$rifle."' size='50'></td></tr>";
				echo "<tr><td colspan='2' style='text-align:center;'><input type='text' name='IPOLMO_OPT_ADDEVENTS[]' value='' size='50'></td></tr>";
				echo "<tr><td colspan='2' style='text-align:center;padding-top:5px;'><input type='button' value='+' size='50' onclick='".$LABEL."setups.getPage(\"events\").addRow()'></td></tr>";
			break;
			default: __AdmSettingsDrawRow($module_id, $Option);break;
		}
			
	}
}

//Save options
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()){
	if(strlen($RestoreDefaults)>0){
		COption::RemoveOption($module_id);
	}else{
		if(array_key_exists('IPOLMO_OPT_LOCATIONDETAILS',$_REQUEST))
			$_REQUEST['IPOLMO_OPT_LOCATIONDETAILS'] = serialize($_REQUEST['IPOLMO_OPT_LOCATIONDETAILS']);
		foreach($_REQUEST['IPOLMO_OPT_ADDEVENTS'] as $key => $val)
			if(!$val)
				unset($_REQUEST['IPOLMO_OPT_ADDEVENTS'][$key]);
		$_REQUEST['IPOLMO_OPT_ADDEVENTS'] = serialize($_REQUEST['IPOLMO_OPT_ADDEVENTS']);
		if(!array_key_exists('IPOLMO_OPT_EVENTS',$_REQUEST))
			$_REQUEST['IPOLMO_OPT_EVENTS'] = '';
		else
			$_REQUEST['IPOLMO_OPT_EVENTS'] = implode(',',$_REQUEST['IPOLMO_OPT_EVENTS']);
		foreach($arAllOptions as $aOptGroup)
			foreach($aOptGroup as $option){
				switch($option[0])
				{
					case 'IPOLMO_OPT_WORKMODE': 
						if($_POST['IPOLMO_OPT_WORKMODE']) COption::SetOptionString($module_id,'IPOLMO_OPT_WORKMODE',$_POST['IPOLMO_OPT_WORKMODE']);break;
					default: __AdmSettingsSaveOption($module_id, $option);break;
				}
			}
	}
	if($_REQUEST["back_url_settings"] <> "" && $_REQUEST["Apply"] == "")
		   echo '<script type="text/javascript">window.location="'.CUtil::addslashes($_REQUEST["back_url_settings"]).'";</script>';		
}

if(!preg_match('/\{([^\}]+)\}/',COption::GetOptionString($module_id,"IPOLMO_OPT_PROPS"))){
	$exceptText = "<br>".Ipolh\MO\Tools::getMessage('FNDD_ERR_NOPROPS');
}
if(strlen(COption::GetOptionString($module_id,"IPOLMO_OPT_EVENTS","SALE_NEW_ORDER"))<1){
	$exceptText .= "<br>".Ipolh\MO\Tools::getMessage('FNDD_ERR_NOEVENT');
}

if($exceptText){?>
	<table><?Ipolh\MO\Tools::placeErrorLabel($exceptText);?></table>
<?}?>
<?Ipolh\MO\Tools::getCommonCss();?>
<style>
     .ipol_header {
         font-size: 16px;
         cursor: pointer;
         display:block;
         color:#2E569C;
     }
    .ipol_inst {
        display:none;
        margin-left:10px;
        margin-top: 10px;
        margin-bottom: 10px;
        color: #555;
    }
    .ipol_smallHeader{
        cursor: pointer;
        display:block;
        color:#2E569C;
    }
    .ipol_subFaq{
        margin-bottom:10px;
    }

    .<?=$LABEL?>headerLink{
        cursor: pointer;
        text-decoration: underline;
    }
    img{border: 1px dotted black;}
	
	
</style>
<script type="text/javascript" src="<?=Ipolh\MO\Tools::getJSPath()?>adminInterface.js"></script>
<script>
var <?=$LABEL?>setups = new imo_adminInterface({
        'ajaxPath' : '<?=Tools::getJSPath()?>ajax.php',
        'label'    : '<?=$module_id?>',
        'logging'  : true
    });
$(document).ready(<?=$LABEL?>setups.init);
</script>

<?foreach(array('MARKPROPS','WORKMODE','LOCATIONDETAILS') as $popup){
	Ipolh\MO\Tools::placeHint($popup);
}?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
<?
$tabControl->Begin();
foreach($arTabs as $arTab){
	$tabControl->BeginNextTab();
	include_once($_SERVER['DOCUMENT_ROOT'].$arTab["PATH"]);
}
$tabControl->Buttons();
?>
<script language="JavaScript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<div align="left">
	<input type="hidden" name="Update" value="Y">
	<input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
</div>
<?$tabControl->End();?>
<?=bitrix_sessid_post();?>
</form>