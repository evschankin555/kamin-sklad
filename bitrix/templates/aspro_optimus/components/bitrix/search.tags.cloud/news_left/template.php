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
<div class="news__tegs news_blocks front">
	<div class="top_block">
		<div class="title_block">Теги</div>
	</div>
	<ul>
        <?if ($_GET['set_filter'] != ""):?>
        	<li><a href="./">Все</a></li>
        <?else:?>
            <li class="active"><a href="./">Все</a></li>
        <?endif;?>
	<?
		foreach ($arResult["SEARCH"] as $key => $res)
		{		
			if (strpos("|".$arParams["COLOR_OLD"]."|","|".$res["NAME"]."|")!==false)
			{
				?>
				<li class="active"><a href="<?=(($arParams["COLOR_OLD"]==$res["NAME"])?"./":"?set_filter=Y&arrFilter_ff[TAGS]=".$res["NAME"]."")?>"><?=$res["NAME"]?></a>
				<?				
			}
			else
			{
				?>
				<li><a href="<?=(($arParams["COLOR_OLD"]==$res["NAME"])?"./":"?set_filter=Y&arrFilter_ff[TAGS]=".$res["NAME"]."")?>"><?=$res["NAME"]?></a></li>
				<?				
			}
		}
	?>
	</ul>
</div>
<?
endif;
?>