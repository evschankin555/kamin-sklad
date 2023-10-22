<?

define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$GLOBALS['APPLICATION']->RestartBuffer();

$domain = '//'.$_SERVER['SERVER_NAME'];

require_once $_SERVER['DOCUMENT_ROOT'].'/301/xls/PHPExcel.php';
$fileName = $_SERVER['DOCUMENT_ROOT'].'/301/301.xls';

$Excel = PHPExcel_IOFactory::load( $fileName );
$Excel->setActiveSheetIndex(0);

$toUrl = '/';

$i = 1;

while( ( $fromUrl = trim( $Excel->getActiveSheet()->getCell( 'A'.$i )->getValue() ) ) != '' ) {
  if( $fromUrl == $_GET['PAGE'] ) {
    $toUrl = trim( $Excel->getActiveSheet()->getCell( 'B'.$i )->getValue() );
    break;
  }
  $i++;
}

header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$domain.$toUrl);
exit();

?>
