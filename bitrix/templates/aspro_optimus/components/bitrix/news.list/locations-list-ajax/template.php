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
?>
	<div class="popup-intro">
		<div class="pop-up-title">Выберете Ваш город</div>
	</div>
	<a class="jqmClose close"><i></i></a>
	<div class="form-wr">
    
        <div class="locations-list">
            <ul>
                <? foreach($arResult["ITEMS"] as $arItem){?>
<li><a href="#" onclick="return setGetParameter('changeloc','<?=$arItem['ID']?>', '<?=$arItem['PROPERTIES']["domain"]["VALUE"]?>');" data-code="<?=$arItem['CODE']?>"><?echo $arItem['NAME']?></a></li>
                <? } ?>
            </ul>
        </div>
	</div>
<script type="text/javascript">

	$(document).ready(function(){
		$('.locations-list a').on('click', function(e){
			location.href=$(this).attr('href');
		});
	});
	$('.popup').jqmAddClose('.jqmClose');
</script>