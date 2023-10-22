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
