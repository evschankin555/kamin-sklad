<?if(!check_bitrix_sessid()) return;?>
<?
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

\CAdminNotify::Add([
    'MESSAGE' => Loc::getMessage('TWIM.RECAPTCHAFREE_DONATE_MESSAGE', ['#URL#' => '/bitrix/admin/settings.php?lang=' . LANG . '&mid=twim.recaptchafree&tabControl_active_tab=donate']),
    'TAG' => 'twim.recaptchafree_install',
    'MODULE_ID' => 'twim.recaptchafree',
]);

echo CAdminMessage::ShowNote(Loc::getMessage("TWIM.RECAPTCHAFREE_INSTALL_TITLE"));
?>
<form action="/bitrix/admin/settings.php">
	<input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="hidden" name="mid" value="twim.recaptchafree">
    <a class="adm-btn adm-btn-save" href="/bitrix/admin/settings.php?lang=<?echo LANG?>&mid=twim.recaptchafree&tabControl_active_tab=donate"><?=Loc::getMessage("TWIM.RECAPTCHAFREE_LINK_DANATE")?></a>
	<input class="adm-btn" type="submit" name="" value="<?=Loc::getMessage("TWIM.RECAPTCHAFREE_LINK_SETTINGS")?>">
    <a class="adm-btn" href="/bitrix/admin/partner_modules.php?lang=<?echo LANG?>"><?=Loc::getMessage("TWIM.RECAPTCHAFREE_LINK_BACK")?></a>
</form>
