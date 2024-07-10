<?
$catalog_id=\Bitrix\Main\Config\Option::get("aspro.optimus", "CATALOG_IBLOCK_ID", COptimusCache::$arIBlocks[SITE_ID]['aspro_optimus_catalog']['aspro_optimus_catalog'][0]);
// Создаем объект класса COptimusCache для вызова метода CIBlockSection_GetList()
$optimusCache = new COptimusCache();

// Вызываем метод CIBlockSection_GetList() через объект $optimusCache
$arSections = $optimusCache->CIBlockSection_GetList(
    array(
        'SORT' => 'ASC',
        'ID' => 'ASC',
        'CACHE' => array(
            'TAG' => $optimusCache->GetIBlockCacheTag($catalog_id),
            'GROUP' => array('ID')
        )
    ),
    array(
        'IBLOCK_ID' => $catalog_id,
        'ACTIVE' => 'Y',
        array(
            "LOGIC" => "OR",
            array("UF_REGIONS" => false),
            array("!UF_REGIONS" => false, "UF_REGIONS" => $arParams["REGIONS"])
        ),
        'GLOBAL_ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        '<DEPTH_LEVEL' => \Bitrix\Main\Config\Option::get("aspro.optimus", "MAX_DEPTH_MENU", 2)
    ),
    false,
    array("ID", "NAME", "PICTURE", "LEFT_MARGIN", "RIGHT_MARGIN", "DEPTH_LEVEL", "SECTION_PAGE_URL", "IBLOCK_SECTION_ID")
);

if($arSections){
	$arResult = array();
	$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
	$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);

	foreach($arSections as $ID => $arSection){
		$arSections[$ID]['SELECTED'] = CMenu::IsItemSelected($arSection['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index);
		if($arSection['PICTURE']){
			$img=CFile::ResizeImageGet($arSection['PICTURE'], Array('width'=>50, 'height'=>50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			$arSections[$ID]['IMAGES']=$img;
		}
		if($arSection['IBLOCK_SECTION_ID']){
			if(!isset($arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'])){
				$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'] = array();
			}
			$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILD'][] = &$arSections[$arSection['ID']];
		}

		if($arSection['DEPTH_LEVEL'] == 1){
			$arResult[] = &$arSections[$arSection['ID']];
		}
	}
}?>