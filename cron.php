<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>

<?php
	$link = mysql_connect('localhost:31006', 'root', '');
	if (!$link) {
		die('Не удалось соединиться : ' . mysql_error());
	}
	
	// выбираем foo в качестве текущей базы данных
	$db_selected = mysql_select_db('kamin-sklad-old', $link);
	if (!$db_selected) {
		die ('Не удалось выбрать базу foo: ' . mysql_error());
	}

    mysql_query("set character_set_client='cp1251'");
    mysql_query("set character_set_results='cp1251'");
    mysql_query("set collation_connection='cp1251_general_ci'");

	$ids=array();
	$db = mysql_query("select * from larionov_nabor");
	while ($row = mysql_fetch_array($db)) {

		$arFields=array();
		$arFields2=array();
		$arFilter = Array('IBLOCK_ID'=>14, 'XML_ID'=>$row["good_m"], 'ACTIVE'=>"Y");
		$res = CIBlockElement::GetList(Array($by=>$order), $arFilter, false, false, array("ID","CATALOG_GROUP_1","NAME"));
		if($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$arFields["price"] = $arFields["CATALOG_PRICE_1"];	
			$arFields["new_price"] = $row["main_price"];		
		}
		$arFilter2 = Array('IBLOCK_ID'=>14, 'XML_ID'=>$row["id_good"], 'ACTIVE'=>"Y");
		$res2 = CIBlockElement::GetList(Array($by=>$order), $arFilter2, false, false, array("ID","CATALOG_GROUP_1","NAME"));
		if($ob2 = $res2->GetNextElement())
		{
			$arFields2 = $ob2->GetFields();
			$arFields2["price"] = $arFields2["CATALOG_PRICE_1"];				
			$arFields2["new_price"] = $row["price"];								
		}	
				
		
		if (count($arFields)>0 && count($arFields2)>0)
		{					
				$arFieldsCustom = array('LID' => "s1", 'NAME' => "Набор: ".$arFields["NAME"]." + ".$arFields2["NAME"]."", 'ACTIVE' => 'Y', 'ACTIVE_FROM' => '', 'ACTIVE_TO' => '', 'PRIORITY' => 1, 'SORT' => 100, 'XML_ID' => $xmlId, 'USER_GROUPS' => array(2));	
				$arFieldsCustom["CONDITIONS"]=serialize(Array
(
    "CLASS_ID" => "CondGroup",
    "DATA" => Array
        (
            "All" => "AND",
            "True" => "True"
        ),
    "CHILDREN" => Array
        (
            "0" => Array
                (
                    "CLASS_ID" => "CondGroup",
                    "DATA" => Array
                        (
                            "All" => "AND",
                            "True" => "True"
                        ),
                    "CHILDREN" => Array
                        (
                            "0" => Array
                                (
                                    "CLASS_ID" => "CondBsktProductGroup",
                                    "DATA" => Array
                                        (
                                            "Found" => "Found",
                                            "All" => "AND"
                                        ),
                                    "CHILDREN" => Array
                                        (
                                            "1" => Array
                                                (
                                                    "CLASS_ID" => "CondBsktFldProduct",
                                                    "DATA" => Array
                                                        (
                                                            "logic" => "Equal",
                                                            "value" => $arFields["ID"]
                                                        )

                                                )

                                        )

                                ),
                            "1" => Array
                                (
                                    "CLASS_ID" => "CondBsktProductGroup",
                                    "DATA" => Array
                                        (
                                            "Found" => "Found",
                                            "All" => "AND"
                                        ),
                                    "CHILDREN" => Array
                                        (
                                            "1" => Array
                                                (
                                                    "CLASS_ID" => "CondBsktFldProduct",
                                                    "DATA" => Array
                                                        (
                                                            "logic" => "Equal",
                                                            "value" => $arFields2["ID"]
                                                        )

                                                )

                                        )
                                )
                        )
                )
        )
));
echo("Набор: ".$arFields["NAME"]." + ".$arFields2["NAME"]."");
echo("<br>");
echo(round(($arFields["price"]-$arFields["new_price"])*100/$arFields["price"],2));
echo("<br>");
echo(round(($arFields2["price"]-$arFields2["new_price"])*100/$arFields2["price"],2));
echo("<br>");
echo("<br>");

				$arFieldsCustom["ACTIONS"]=serialize(Array
(
    "CLASS_ID" => "CondGroup",
    "DATA" => Array
        (
            "All" => "AND"
        ),
    "CHILDREN" => Array
        (
            "1" => Array
                (
                    "CLASS_ID" => "ActSaleBsktGrp",
                    "DATA" => Array
                        (
                            "Type" => "Discount",
                            "Value" => round(($arFields["price"]-$arFields["new_price"])*100/$arFields["price"],2),
                            "Unit" => "Perc",
                            "All" => "AND"
                        ),
                    "CHILDREN" => Array
                        (
                            "0" => Array
                                (
                                    "CLASS_ID" => "CondIBElement",
                                    "DATA" => Array
                                        (
                                            "logic" => "Equal",
                                            "value" => Array
                                                (
                                                    "0" => $arFields["ID"]
                                                )
                                        )
                                )
                        )
                ),
            "2" => Array
                (
                    "CLASS_ID" => "ActSaleBsktGrp",
                    "DATA" => Array
                        (
                            "Type" => "Discount",
                            "Value" => round(($arFields2["price"]-$arFields2["new_price"])*100/$arFields2["price"],2),
                            "Unit" => "Perc",
                            "All" => "AND"
                        ),
                    "CHILDREN" => Array
                        (
                            "0" => Array
                                (
                                    "CLASS_ID" => "CondIBElement",
                                    "DATA" => Array
                                        (
                                            "logic" => "Equal",
                                            "value" => Array
                                                (
                                                    "0" => $arFields2["ID"]
                                                )
                                        )
                               )
                        )
                )
        )
));
				//CSaleDiscount::Add($arFieldsCustom);			
//exit();
		}
	}   

	  
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>