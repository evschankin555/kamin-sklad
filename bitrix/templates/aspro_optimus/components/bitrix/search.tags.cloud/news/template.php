<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if(is_array($arResult["SEARCH"]) && !empty($arResult["SEARCH"])):
?>
<div class="block tag">
        <?if ($_GET['set_filter'] != ""):?>
        	<a class="button small transparent grey_br" href="/news/">Все</a>
        <?else:?>
            <a class="button small">Все</a>
        <?endif;?>
	<?
		foreach ($arResult["SEARCH"] as $key => $res)
		{		
			if ($arParams["COLOR_OLD"]==$res["NAME"])
			{
				?>
				<a href="<?=(($arParams["COLOR_OLD"]==$res["NAME"])?"./":"?set_filter=Y&arrFilter_ff[TAGS]=".$res["NAME"]."")?>" class="button small"><?=$res["NAME"]?></a>
				<?				
			}
			else
			{
				?>
				<a href="<?=(($arParams["COLOR_OLD"]==$res["NAME"])?"./":"?set_filter=Y&arrFilter_ff[TAGS]=".$res["NAME"]."")?>" class="button small transparent grey_br"><?=$res["NAME"]?></a>
				<?				
			}
		}
	?>
</div><br>
<?
endif;
?>