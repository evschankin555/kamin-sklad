<?

use Ipolh\MO\Tools;
use Ipolh\MO\option;

IncludeModuleLangFile(__FILE__);
class mailorderdriver
{
	static $MODULE_ID = "ipol.mailorder";
	
	static $arFields  = false;
	static $arSetups  = false;

	function insertMacrosData(&$event, &$lid, &$arFields){
		if(!cmodule::includeModule('sale'))
			return;

		$chozen = explode(',',Ipolh\MO\option::get("IPOLMO_OPT_EVENTS"));
		$manual = unserialize(Ipolh\MO\option::get("IPOLMO_OPT_ADDEVENTS"));
		$rightEvent = true;

		if(!in_array($event,$chozen) && !in_array($event,$manual)){
			if(strpos($event,"SALE_STATUS_CHANGED")===0 && in_array("SALE_STATUS_CHANGED",$chozen))
				$rightEvent = true;
			else
				$rightEvent = false;
		}

		if(strlen($arFields['ORDER_ID'])<=0 || !$rightEvent || !CModule::includeModule('sale'))
			return;
		
		self::$arFields = array();

		$orderId = $arFields['ORDER_ID'];
		$checkAN = CSaleOrder::GetList(array(),array('ACCOUNT_NUMBER'=>$orderId))->Fetch();
		if($checkAN)
			$orderId=$checkAN['ID'];

		self::setSetups();

		$orderProps = CSaleOrderPropsValue::GetOrderProps($orderId);

		while($prop=$orderProps->Fetch()){
			if(in_array($prop['CODE'], self::$arSetups) && $prop['VALUE']){ // == MOD ищем в массиве
				$name  = $prop['NAME'];
				$value = $prop['VALUE'];
				switch($prop['TYPE']){
					case 'LOCATION' : $value = self::getLocation($value);
									  break;
					case 'RADIO'	: 
					case 'SELECT'   : $value = CSaleOrderPropsVariant::GetByValue($prop['ORDER_PROPS_ID'],$value);
								      $value = $value['NAME'];
									  break;
					case 'CHECKBOX' : $name = '';
									  break;
					case 'FILE'     : $file = CFile::GetFileArray($value);
									  $value = "<a href='http://".$_SERVER['HTTP_HOST'].$file['SRC']."'; >".$file['ORIGINAL_NAME']."</a>";
									  break;
				}

				self::toFields('IPOLMO_'.$prop['CODE'],$value,$name);
			}
		}
		foreach(self::$arSetups as $val){
			if(!isset(self::$arFields['IPOLMO_'.$val]))
				self::toFields('IPOLMO_'.$val,'','');
		}
		
		$orderHimself=CSaleOrder::GetByID($orderId);
		// платежная система
		if(self::checkParam('IMOPAYSYSTEM')){
			$paySystem=CSalePaySystem::GetByID($orderHimself['PAY_SYSTEM_ID']);
			self::toFields('IPOLMO_IMOPAYSYSTEM',$paySystem['NAME'],Tools::getMessage("OPT_PROPS_PAYSYSTEM"));
		}
		// доставка
		if(self::checkParam('IMODELIVERY')){
			if(Ipolh\MO\Tools::isConverted()){
				$orderInfo = Bitrix\Sale\Order::load($orderId);
				$ds = $orderInfo->getDeliverySystemId();
				$ds = Bitrix\Sale\Delivery\Services\Table::getList(array('filter'=>array('ID' =>$ds[0])))->Fetch();
				if($ds)
					$deliveryName=$ds['NAME'];
			}else{
				if(strpos($orderHimself['DELIVERY_ID'],':')){
					$deliveryId=explode(':',$orderHimself['DELIVERY_ID']);
					if($deliverySystem=CSaleDeliveryHandler::GetBySID($deliveryId[0])->Fetch()){
						$deliveryName=$deliverySystem['NAME'];
						if($deliverySystem['PROFILES'][$deliveryId[1]]['TITLE'])
							$deliveryName.=" (".$deliverySystem['PROFILES'][$deliveryId[1]]['TITLE'].")";
					}
					else
						$deliveryName=false;
				}else{
					$deliverySystem=CSaleDelivery::GetByID($orderHimself['DELIVERY_ID']);
					$deliveryName=$deliverySystem['NAME'];
				}
			}
			if($deliveryName){
				self::toFields('IPOLMO_IMODELIVERY',$deliveryName,Tools::getMessage("OPT_PROPS_DELIVERY"));
			}
		}
		// стоимость доставки
		if(self::checkParam('IMODELIVERYPRICE')){
			$deliveryPrice = $orderHimself['PRICE_DELIVERY'];
			if(cmodule::includeModule('currency'))
				$deliveryPrice = CCurrencyLang::CurrencyFormat($deliveryPrice,$orderHimself['CURRENCY'],true);
			self::toFields('IPOLMO_IMODELIVERYPRICE',$deliveryPrice,Tools::getMessage("OPT_PROPS_DELIVERYPRC"));
		}
		// документ об оплате
		if(self::checkParam('IMOPAYED')){
			$strOfPayed=false;
			if($orderHimself['PAY_VOUCHER_NUM']){
				$strOfPayed=Tools::getMessage("SIGN_PAYDOC").$orderHimself['PAY_VOUCHER_NUM'];
				if(preg_match('/([\d]{4})-([\d]{2})-([\d]{2})/',$orderHimself['PAY_VOUCHER_DATE'],$matches))
					$strOfPayed.=" ".Tools::getMessage("SIGN_FROM").$matches[3].".".$matches[2].".".$matches[1];
			}
			if($strOfPayed){
				self::toFields('IPOLMO_IMOPAYED',$strOfPayed,'');
			}
		}
		// идентификатор отправления
		if(self::checkParam('IMOTRACKING')){
			if($orderHimself['TRACKING_NUMBER']){
				self::toFields('IPOLMO_IMOTRACKING',$orderHimself['TRACKING_NUMBER'],Tools::getMessage("SIGN_TRACKING"));
			}
		}		
		// Сумма заказа
		if(self::checkParam('IMOPRICE')){
			$strOfPayed=CCurrencyLang::CurrencyFormat($orderHimself['PRICE'],$orderHimself['CURRENCY'],true);
			if($strOfPayed){
				self::toFields('IPOLMO_IMOPRICE',$strOfPayed,Tools::getMessage("SIGN_PRICE"));
			}
		}
		// Комментарий покупателя
		if(self::checkParam('IMOCOMMENT')){
			if($orderHimself['USER_DESCRIPTION']){
				self::toFields('IPOLMO_IMOCOMMENT',$orderHimself['USER_DESCRIPTION'],Tools::getMessage("SIGN_COMMENT"));
			}
		}
		
		$arAdditionalFields = array();
		foreach(GetModuleEvents(self::$MODULE_ID, "getAdditionalFields", true) as $arEvent){
			$arAdditionalFields = ExecuteModuleEventEx($arEvent,Array($event,$orderId,$arFields,$lid,self::$arFields));
		}

		if(is_array($arAdditionalFields) && count($arAdditionalFields)){
			foreach($arAdditionalFields as $key => $arAdditionalField){
				if(strpos($key,'IPOLMO_') !== false){
					self::toFields($key,$arAdditionalField['VALUE'],$arAdditionalField['NAME']);
				}
			}
		}
self::toLog(self::$arSetups,'self::$arSetups');
self::toLog(self::$arFields,'self::$arFields');
		$mode=Ipolh\MO\option::get("IPOLMO_OPT_WORKMODE");
		if($mode=='1'){
			$arFields=array_merge($arFields,self::getFieldsArray());
		}if($mode=='2')
			$arFields['IPOLMOALL_PROPS']=self::getFieldsString();
self::toLog($arFields,'$arFields');
	}

	// Вспомогательный функционал
	
	protected function setSetups(){
		self::$arSetups = array();
		
		$arSavedProps=explode('|',Ipolh\MO\option::get("IPOLMO_OPT_PROPS"));
		$savedProps='';
		foreach($arSavedProps as $propStr)
			$savedProps.=','.substr($propStr,strpos($propStr,'{')+1,strpos($propStr,'}')-strpos($propStr,'{')-1);
		$savedProps=str_replace(',,',',',$savedProps);

		$arSavedProps = explode(',', $savedProps);
		foreach($arSavedProps as $key=>$val) {
			$val = trim($val);
			$val = trim(substr($val, 0, strpos($val, ' ')));
			if(!$val) unset($arSavedProps[$key]); else $arSavedProps[$key] = $val;
		}
		
		self::$arSetups = $arSavedProps;
	}
	
	protected function toFields($code,$val,$name){
		self::$arFields [$code]= array(
			'NAME'  => $name,
			'VALUE' => $val
		);
	}
	
	protected function getFieldsArray(){
		$arFields = array();
		foreach(self::$arFields as $macros => $arField){
			$arFields [$macros] = $arField['VALUE'];
		}
		return $arFields;
	}
	
	protected function getFieldsString(){
		$string = '';
		$newStringSign = (Ipolh\MO\option::get("IPOLMO_OPT_TEXTMODE")=='2') ? "<br>" : "\n";
		foreach(self::$arFields as $macros => $arField){
			$newString = '';
			if($arField['NAME']){
				$newString = $arField['NAME'].' - ';
			}
			if($arField['VALUE']){
				$newString .= $arField['VALUE'];
			}
			if($newString){
				$string .= $newString.$newStringSign;
			}
		}

		return $string;
	}
	
	protected function checkParam($param){
		return (in_array($param,self::$arSetups));
	}
	
	// LOCATIONS

	function isNewLocations(){
		return (Ipolh\MO\Tools::isConverted()  && method_exists("CSaleLocation","isLocationProMigrated") && CSaleLocation::isLocationProMigrated());
	}

	function getLocationTypes(){
		if(!cmodule::includeModule('sale'))
			return false;
		if(Ipolh\MO\Tools::isConverted()){
			$arLocations = array();
			$locTypes = \Bitrix\Sale\Location\TypeTable::getList(array('select'=>array('CODE','LBL'=>'NAME.NAME'),'filter'=>array('NAME.LANGUAGE_ID' => LANGUAGE_ID)));
			while($element=$locTypes->Fetch())
				$arLocations[$element['CODE']] = $element['LBL'];
		}else
			$arLocations = array(
				'COUNTRY' => Tools::getMessage('SIGN_COUNTRY'),
				'REGION'  => Tools::getMessage('SIGN_REGION'),
				'CITY'    => Tools::getMessage('SIGN_CITY')
			);
		return $arLocations;
	}

	function getLocation($location){
		$place = '';
		$separator = COption::GetOptionString(self::$MODULE_ID,'IPOLMO_OPT_LOCATIONSEPARATOR',', ');
		$svd = unserialize(COption::GetOptionString(self::$MODULE_ID,'IPOLMO_OPT_LOCATIONDETAILS',self::getDefLocationTypes()));
		if(self::isNewLocations()){
			if(\Bitrix\Main\Loader::includeModule('sale')){
				if(strlen($location) == 10)
					$arFilter = array('=CODE' => $location);
				else
					$arFilter = array('=ID'=>$location);
				$result = \Bitrix\Sale\Location\LocationTable::getPathToNodeByCondition($arFilter, array(
					'select' => array('CHAIN' => 'NAME.NAME','DETAIL'=>'TYPE.CODE'),
					'filter' => array('NAME.LANGUAGE_ID' => LANGUAGE_ID)
				));
				while($element=$result->Fetch())
					if(in_array($element['DETAIL'],$svd))
						$place .= $element['CHAIN'].$separator;
			}
		}elseif(cmodule::includeModule('sale')){
			if(Ipolh\MO\Tools::isConverted())
				$location = CSaleLocation::getLocationIDbyCODE($prop['VALUE']);
			$location = CSaleLocation::GetByID($location);
			foreach($svd as $code)
				if($location[$code.'_NAME_LANG'])
					$place .= $location[$code.'_NAME_LANG'].$separator;
		}
		return substr($place,0,(strlen($place) - strlen($separator)));
	}

	function getDefLocationTypes(){
		return serialize(array('COUNTRY','REGION','CITY'));
	}
	
	static function toLog($wat,$sign=''){
        if($sign) $sign.=" ";
        if(!$GLOBALS['ipolmo_logfile']){
            $GLOBALS['ipolmo_logfile'] = fopen($_SERVER['DOCUMENT_ROOT'].'/IMOLog.txt','w');
            fwrite($GLOBALS['ipolmo_logfile'],"\n\n".date('H:i:s d.m')."\n");
        }
        fwrite($GLOBALS['ipolmo_logfile'],$sign.print_r($wat,true)."\n");
    }
}
?>