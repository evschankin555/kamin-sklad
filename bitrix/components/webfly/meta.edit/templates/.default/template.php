<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($USER->IsAdmin()): ?>

    <a id="wfSeoEditLink" href="#" class='wfbtn'><?= GetMessage('SET_META') ?></a>

    <div id="wfSeoEditFormContainer">
        <form id="wfSeoEditForm" action="" method="post">
            <input type="hidden" name="PageId" value="<?= $arResult['ID'] ?>"/>
            <input type="hidden" name="wfSeoEditSave" value="Y"/>
            <p><?= GetMessage('URL') ?>:<br /> <input type="text" name="PageUrl" value="<?= $arResult['PAGE'] ?>"/></p>
            <p><?= GetMessage('H1') ?>:<br /> <input type="text" name="PageH1" value="<?= $arResult['H1'] ?>"/></p>
            <p><?= GetMessage('TITLE') ?>:<br /> <input type="text" name="PageTitle" value="<?= $arResult['TITLE'] ?>"/></p>
            <p><?= GetMessage('ROBOTS') ?>:<br /> <input type="text" name="PageRobots" value="<?= $arResult['ROBOTS'] ?>"/></p>
            <p><?= GetMessage('DESCRIPT') ?>:<br /> <textarea name="PageDescription" cols="30" rows="10"><?= $arResult['DESCRIPTION'] ?></textarea></p>
            <p><?= GetMessage('KEYWORDS') ?>:<br /> <textarea name="PageKeywords" cols="30" rows="10"><?= $arResult['KEYWORDS'] ?></textarea></p>
            <input type="submit" name="save" value="<?= GetMessage('SAVE') ?>" id="wfsave"/>
            <input type="submit" name="save_and_to_admin" value="<?= GetMessage('SAVE_AND_TO_ADMIN') ?>" id="wfsavetoadmin"/>
        </form>
    </div>
<? endif; ?>