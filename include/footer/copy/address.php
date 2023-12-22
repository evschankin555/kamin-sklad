<p>Наш адрес:
<span><?=$_SESSION['CURRENT_LOCATION']['CURRENT']["ADDRESS"]?></span></p>

<p><?
$arrTime = explode("\n",$_SESSION['CURRENT_LOCATION']['CURRENT']["TIME"]);
$k=0;
foreach($arrTime as $k=>$v)
{
	if ($k!=0) echo("<span>");
	echo $v;
	if ($k!=0) echo("</span>");
	$k++;
}
?></p>
