<?
foreach ($_SESSION['CURRENT_LOCATION']['CURRENT']["PHONES"] as $k=>$v)
{
	echo("<a class=\"mgo-number-21684\" onclick=\"ym(32406990, 'reachGoal', 'TOP_PHONE_CLICK'); return true;\" href=\"tel:".str_replace("(","",str_replace(")","",str_replace(" ","",$v)))."\" rel=\"nofollow\">".$v."</a>\n");
}
?>