<?
// Подключение Битрикса:
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
define("LANG", "s1");
define("BX_UTF", true);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_BUFFER_USED", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


// Начало функционала:
//$PRODUCT_ID = 1471;  // для тестирования
$PRICE_TYPE_ID = 1;//1 - Москва
//$ar_res = CCatalogProduct::GetByIDEx($PRODUCT_ID); // для тестирования

$fregat = getFregat(); // обновляем на нашем сервере файл выгрузки из Фрегата
$products = getProductList(116); // получение списка товаров из каталога сайта. Берём только обычные товары (без товарных предложений!)

$counter = 0;
$update_list = array();
foreach ($fregat as $price_key => $price_value) { // обход массива данных из Фрегата со сверкой
    $found = false;
    foreach ($products as $sku_key => $sku_value) {
        if ( trim($price_value['CODE']) == trim($sku_value['PROPERTIES']['CML2_ARTICLE']['VALUE']) && $price_value['CODE'] != ''){
            $res = CPrice::GetList(
                array(),
                array(
                        "PRODUCT_ID" => $sku_value['ID'],
                        "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
                    )
            );
            $arFields = Array(
                "PRODUCT_ID" => $sku_value['ID'],
                "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
                "PRICE" => $price_value['PRICE'],
                //"CURRENCY" => "EUR",
                //"QUANTITY_FROM" => 1,
                //"QUANTITY_TO" => 10
            );
            if ($arr = $res->Fetch())
            {
                CPrice::Update($arr["ID"], $arFields); // Обновление цены у товара!
                $update_list[] = $sku_value['NAME'];
                $counter++;
            }
        }
    }

}
$file = 'update_log.txt';
$add = print_r($update_list, true);
// Write the contents to the file, 
// using the FILE_APPEND flag to append the content to the end of the file
// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
file_put_contents($file, $add, LOCK_EX);
echo '<h3>Обновлено товаров: '.$counter. ' </h3>';



function getFregat()
{
    $debug = '';
    $SITE_ROOT = $_SERVER["DOCUMENT_ROOT"];
    $ftp = ftp_connect("infobumo.beget.tech", "21", "30"); // Создаём идентификатор соединения (адрес хоста, порт, таймаут)
    $login = ftp_login($ftp, "infobumo_pmc", "tvS&4Kz6"); // Авторизуемся на FTP-сервере
    if ( !$login )
    {
        $debug .=  'Ошибка подключения!<br>'; 
        ftp_close($ftp);
        echo $debug;
        return;
    }
    else
    {
        $debug .=  'Подключено.<br>'; 
        ftp_pasv($ftp, true); 
        $prices_path = $SITE_ROOT . "/upload/prices/";
        $debug .= 'Директория файлов с ценами: ' . $prices_path;
        $debug .= '<br/>';
        $debug .= 'Локальная директория: ' . __DIR__;
        $debug .= '<br/>';
        $debug .= 'FTP директория: ' . ftp_pwd($ftp);
        $debug .= '<br/>';
        ftp_chdir($ftp, "pmc.art-rym.ru/fregat");
        $debug .= 'FTP директория: ' . ftp_pwd($ftp);
        $debug .= '<br/>';

        $files = ftp_nlist($ftp, "."); // Получаем список файлов из текущей директории
        $debug .= '<pre>';
        $debug .= print_r($files, true);
        $debug .= '</pre><br/>';
        if( ftp_get( $ftp, $prices_path . "fregat.csv", "GOODS.csv", FTP_ASCII ) )
            $debug .=  'Файл скопирован.<br>';
        else
            $debug .=  'Ошибка копирования файла!<br>';        
    }
    ftp_close($ftp);
    
    $csv = array_map('str_getcsv', file($prices_path . "fregat.csv"));
    foreach ($csv as $key => &$value) {
        $row = explode(";", $value[0]);
        $value['ID'] = $row[0];
        $value['CODE'] = $row[1];
        $value['PRICE'] = $row[2];
        $value['QTY'] = $row[3];
    }
    $debug .= '<pre>' . print_r($csv, true) . '</pre>';
    
    if ( $csv )
    {
       
        $debug .= 'OK';
        $debug .= '<h4>Данные файла Фрегат:</h4><ol>';
        $debug .= '<li>Количество артикулов: ' . (count($csv)-1) . '</li>';
        $debug .= '</ol>';

    }
    else
    {
        
        $debug .= 'ERROR';
        
    }

    //echo $debug;
    return $csv;
}


function getProductList($IBLOCK_SECTION_ID = 116)
{
    $my_elements = CIBlockElement::GetList (
        Array("ID" => "ASC"),
        Array('IBLOCK_SECTION_ID' => $IBLOCK_SECTION_ID),
        false,
        false,
        Array('ID', 'NAME', 'DETAIL_PAGE_URL')
    );
    $ret = array();
    while($ar_fields = $my_elements->GetNext())
    {
        $ar_res = CCatalogProduct::GetByIDEx($ar_fields['ID']);
        $ret[] = $ar_res;
    }
    return $ret;
}


?>

<? // Завершение служебным кодом Битрикса 
require($_SERVER["DOCUMENT_ROOT"]. "/bitrix/modules/main/include/epilog_after.php");

