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
        
<?foreach($arResult["ITEMS"] as $arItem):
    if(array_key_exists("HIDDEN", $arItem)):
        echo $arItem["INPUT"];
    endif;
endforeach;?>
    
<div class="sale__block">
	<div class="sale__sort">
 		<a href="?" class="all<?=(($_GET["arrSale_ff"]["SECTION_ID"]==$k)?" active":"")?>">Все акции</a>
		<div class="links">
            <?
            foreach($arResult["ITEMS"] as $arItem):
                if(!array_key_exists("HIDDEN", $arItem)):                
                	foreach($arItem["LIST"] as $k=>$v):
						if ($k>0 && strpos($v,".  .")===false) {
							echo("<a href=\"?arrSale_ff[SECTION_ID]={$k}&arrSale_ff[INCLUDE_SUBSECTIONS]=Y&set_filter=Y\" ".(($_GET["arrSale_ff"]["SECTION_ID"]==$k)?" class=\"active\"":"").">".str_replace(" . ","",$v)."</a>");
						}
                	endforeach; 
                endif;
           endforeach;?>
		</div>
	</div>
</div>



