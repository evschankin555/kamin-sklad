<?php
function fn_geoLocation(){
//$_SERVER['REMOTE_ADDR']="79.141.56.68"; //perm
//$_SERVER['REMOTE_ADDR']="5.43.128.1"; //ekat
//$_SERVER['REMOTE_ADDR']="194.226.128.1"; //salda

/*
if ($_GET['tt'])
{
        include("SxGeo.php");
        $SxGeo = new SxGeo($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/aspro_optimus/include/SxGeoCity.dat');
        $ip = $_SERVER['REMOTE_ADDR'];
        
        var_export($SxGeo->getCityFull($ip)); // Вся информация о городе
        var_export($SxGeo->get($ip));         // Краткая информация о городе или код страны (если используется база SxGeo Country)
        var_export($SxGeo->about());          // Информация о базе данных
}    */
//$k=0;
//if ($_SERVER['REMOTE_ADDR']=="5.141.231.58")
//{
//	$k=1;
//	$_SERVER['REMOTE_ADDR']="217.118.79.39";
//	echo($_SERVER['REMOTE_ADDR']);
//}


	if (strlen($_SERVER['REMOTE_ADDR'])>0){
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/aspro_optimus/include/geoip.php");
		$gb = new IPGeoBase();
		$data = $gb->getRecord($_SERVER['REMOTE_ADDR']);
//		if ($k==1) print_r($data);
		if (strlen($data['city'])>0){
			return $data['city'];
			//return iconv("UTF-8","WINDOWS-1251",$data['city']);
		}
	}
}
// Функция для определения города по поддомену
// Функция для определения города по поддомену и редиректа при указании GET-параметра ?changeloc=ID
function fn_cityBySubdomain() {
    $currentDomain = $_SERVER['SERVER_NAME'];

    // Определение поддомена из текущего домена
    preg_match('/^(.*?)\.kamin-sklad\.ru$/i', $currentDomain, $matches);
    $subdomain = isset($matches[1]) ? $matches[1] : '';

    // Извлечение списка поддоменов и символьных кодов городов из инфоблока
    $citySymbolCodeMap = [];

    // Пример получения данных из инфоблока с ID 20 (подставьте свой ID инфоблока)
    $filter = [
        'IBLOCK_ID' => 20,  // ID вашего инфоблока с городами
        'ACTIVE' => 'Y',
    ];

    $select = ['ID', 'NAME', 'CODE'];  // Выбираем поля без телефонов

    $res = CIBlockElement::GetList([], $filter, false, false, $select);
    while ($item = $res->Fetch()) {
        // Получаем все телефоны для текущего элемента инфоблока
        $phones = [];
        $propPhones = CIBlockElement::GetProperty(20, $item['ID'], [], ['CODE' => 'PHONES']);
        while ($phone = $propPhones->Fetch()) {
            $phones[] = $phone['VALUE'];
        }

        $citySymbolCodeMap[$item['CODE']] = [
            'ID' => $item['ID'],
            'NAME' => $item['NAME'],
            'CODE' => $item['CODE'],
            'PHONES' => $phones,  // Добавляем телефоны в массив
        ];
    }

    // Проверяем, есть ли поддомен в нашем списке соответствий
    if (array_key_exists($subdomain, $citySymbolCodeMap)) {
        return $citySymbolCodeMap[$subdomain];
    }

    return false; // Если поддомен не найден в списке
}

// Функция для смены региона и установки телефонов
function fn_changeLocation2() {
    $cityBySubdomain = fn_cityBySubdomain();
    if ($cityBySubdomain) {
        // Устанавливаем текущий регион в сессию
        $_SESSION['CURRENT_LOCATION']['CURRENT'] = [
            'NAME' => $cityBySubdomain['NAME'], // Имя города можно взять из поддомена
            'ID' => $cityBySubdomain['ID'],
            'PHONES' => $cityBySubdomain['PHONES'],  // Добавляем телефоны для текущего региона
        ];

        return true; // Город определен успешно по поддомену
    }

    return false; // Город не определен по поддомену
}

