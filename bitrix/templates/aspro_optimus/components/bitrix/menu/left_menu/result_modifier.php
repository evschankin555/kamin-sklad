<?php
// Создаем экземпляр класса COptimus
$optimusInstance = new COptimus();

// Вызываем нестатический метод через экземпляр класса
$arResult = $optimusInstance->getChilds2($arResult);

if ($arResult) {
    foreach ($arResult as $key => $arItem) {
        if ($arItem["CHILD"]) {
            $arResult[$key]["CHILD"] = $optimusInstance->unique_multidim_array($arItem["CHILD"], "TEXT");
        }
    }
}
?>
