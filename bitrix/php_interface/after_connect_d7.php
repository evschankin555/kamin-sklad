<?
$connection = \Bitrix\Main\Application::getConnection();
//$connection->queryExecute("SET NAMES 'cp1251'");
$connection->queryExecute("SET LOCAL time_zone='".date('P')."'");
$connection->queryExecute("SET NAMES 'utf8'");
$connection->queryExecute('SET collation_connection = "utf8_unicode_ci"');
?>