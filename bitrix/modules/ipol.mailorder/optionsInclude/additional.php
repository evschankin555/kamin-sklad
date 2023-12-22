<style>
[name="IPOLMO_OPT_LOCATIONSEPARATOR"]{
	width: 10px;
}
</style>
<tr class="heading">
	<td colspan="2" valign="top" align="center"><?=Ipolh\MO\Tools::getMessage('OPT_LOCATIONDETAILS')?> <a href="javascript:void(0)" class="<?=$LABEL?>PropHint" onclick="return <?=$LABEL?>setups.popup('pop-LOCATIONDETAILS', this);"></a></td>
</tr>
<tr><td colspan="2">
    <?Ipolh\MO\Tools::placeFAQ('LOCATIONDETAILS')?>
</td></tr>
<?
	$opt = unserialize(Ipolh\MO\option::get('IPOLMO_OPT_LOCATIONDETAILS'));
	$gotted = mailorderdriver::getLocationTypes();
	if(is_array($gotted)){?>
		<tr><td colspan='2'><table class='IPOLMO_detailTable'><tr><th><?=Ipolh\MO\Tools::getMessage('LBL_TYPENAME')?></th><th><?=Ipolh\MO\Tools::getMessage('LBL_DOSHOW')?></th></tr>
		<?foreach($gotted as $id => $name){?>
			<tr><td><?=$name?></td><td style='text-align:center;'><input type='checkbox' name='IPOLMO_OPT_LOCATIONDETAILS[]' value='<?=$id?>' <?=(in_array($id,$opt)) ? 'checked' : ''?>></td></tr>
		<?}?>
		</table></td></tr>
	<?}
	ShowParamsHTMLByArray($arAllOptions["additional"]);
?>