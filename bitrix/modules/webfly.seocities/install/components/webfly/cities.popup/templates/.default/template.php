<link rel="stylesheet" href="<?=$templateFolder?>/styles.min.css">
<p><a href="#modalCities" id="wf-city" class="wf-city"><?=GetMessage("WF_YOUR_CITY")?> <?=$arResult["CURRENT_CITY"]["NAME"]?></a></p>
<div id="modalCities" class="wf-modal-cities wf-modal fade">
    <div class="wf-modal-dialog modal-lg">
        <div class="wf-modal-content">
            <div class="wf-modal-header">
                <p class="wf-modal-title"><?=GetMessage("WF_CHOOSE_YOUR_CITY")?></p>
                <a href="#" class="wf-modal-close" title="<?=GetMessage("WF_CLOSE")?>"></a>
            </div>
            <div class="wf-modal-body">
                <div class="wf-row">
                    <? if ($arResult["FAVORITES_CITIES"]): ?>
                        <ul class="list-unstyled wf-primary-cities" style="">
                            <? foreach ($arResult["FAVORITES_CITIES"] as $favKey => $favCityFields): ?>
                                <li class="pick-location-final" data-locid="<?= $favCityFields["FIELDS"]["ID"] ?>" data-url="<?=$favCityFields["URL"]. $_SERVER['REQUEST_URI']?>"><?= $favCityFields["FIELDS"]["NAME"] ?></li>
                            <? endforeach ?>
                        </ul>
                    <? endif ?>
                </div>
            </div>
            <? if ($arResult["FIRST_LEVEL_SECTIONS"]):?>
            <div class="wf-modal-footer">
                <div class="wf-row">
                    <div class="col-x-4">
                        <ul class="list-unstyled list-districts">
                            <?
                                foreach ($arResult["FIRST_LEVEL_SECTIONS"] as $lvl1Section):
                                    ?>
                                    <li class="pick-location" data-locid="<?= $lvl1Section["ID"] ?>" data-level="1"><?= $lvl1Section["NAME"] ?></li>
                                    <?
                                endforeach;
                            ?>
                        </ul>
                    </div>
                    <div class="col-x-4">
                        <div id="target-regions"></div>
                    </div>
                    <div class="col-x-2">
                        <div class="wf-row" id="target-cities"></div>
                    </div>
                </div>
            </div>
            <?endif?>
        </div>
    </div>
</div>
<script type="text/javascript">
    BX.message({
        locAddress: "<?=$APPLICATION->GetCurDir();?>",
        filePath: "<?=$templateFolder?>/ajax/cities.php"
    });
</script>
<script src="<?=$templateFolder?>/build.min.js"></script>