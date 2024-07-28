<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle('Настройки редиректов');

$nredirectdomain=COption::GetOptionString("main","redirectdomain");

if (!EMPTY($_POST['save'])){
	
	if ($_POST['redirectdomain']=="Y")
		COption::SetOptionString("main","redirectdomain","Y");
	else
		COption::SetOptionString("main","redirectdomain","N");
		
	header("Location: /bitrix/admin/settingskamin.php");
}

?>

<form action="" method="post">

<label for="redirectdomain">    
    <input type="checkbox" name="redirectdomain" id="redirectdomain" value="Y" <?=($nredirectdomain=="Y")?"checked":""?> /> Вкл/Выкл редирект на региональный поддомен/домен при определении IP посетителя
</label></br>

<br>

<input type="submit" value="Сохранить" name="save">
</form>

<style>
.group-seting{
	width: 400px;
    height: auto;
    border: 1px solid gray;
    padding: 20px;
    border-radius: 3%;
	margin-right: 10px;
    float: left;
}
</style>