<?
namespace Ipolh\MO;
/**
 * Class Tools
 * @package Ipolh\MO\
 * Общие методы, упрощающие работу с html и прочими фишками модуля
 */
class Tools
{
	private static $MODULE_ID  = IPOLH_MO;
	private static $MODULE_LBL = IPOLH_MO_LBL;
    // RIGHTS
    protected static $skipAdminCheck = false;

    public static function isAdmin($min = 'W'){
        if(self::$skipAdminCheck) return true;
        $rights = \CMain::GetUserRight(self::$MODULE_ID);
        $DEPTH = array('D'=>1,'R'=>2,'W'=>3);
        return($DEPTH[$min] <= $DEPTH[$rights]);
    }

    /**
     * @return bool
     */
    public static function isSkipAdminCheck()
    {
        return self::$skipAdminCheck;
    }

    /**
     * @param bool $skipAdminCheck
     */
    public static function setSkipAdminCheck($skipAdminCheck)
    {
        self::$skipAdminCheck = $skipAdminCheck;
    }

    // COMMON

    static function getMessage($code)
    {
        return GetMessage('IPOLMO_'.$code);
    }

    static function getJSPath()
    {
        return '/bitrix/js/'.self::$MODULE_ID.'/';
    }

    static function getImagePath()
    {
        return '/bitrix/images/'.self::$MODULE_ID.'/';
    }

    static function arrToJs($array){
        if(!is_array($array))
            return '"'.$array.'"';
        else{
            $ret = '{';
            foreach($array as $key => $value){
                $ret .= '"'.$key.'":'.self::arrToJs($value).',';
            }
            $ret = substr($ret,0,strlen($ret)-1);
            $ret .= '}';
            return $ret;
        }
    }

    public static function jsonEncode($wat)
    {
        return json_encode(self::encodeToJSON($wat));
    }

    public static function encodeToJSON($handle){
        if(LANG_CHARSET !== 'UTF-8') {
            if (is_array($handle)) {
                foreach ($handle as $key => $val) {
                    unset($handle[$key]);
                    $key          = self::encodeToJSON($key);
                    $handle[$key] = self::encodeToJSON($val);
                }
            } elseif (is_object($handle)){
                $arCorresponds = array(); // why = because
                foreach($handle as $key => $val){
                    $arCorresponds[$key] = array(self::encodeToJSON($key),self::encodeToJSON($val));
                }
                foreach($arCorresponds as $key => $new)
                {
                    unset($handle->$key);
                    $handle->$new[0] = $new[1];
                }
            }else {
                $handle = $GLOBALS['APPLICATION']->ConvertCharset($handle, LANG_CHARSET, 'UTF-8');
            }
        }
        return $handle;
    }

    public static function encodeFromJSON($handle){
        if(LANG_CHARSET !== 'UTF-8'){
            if(is_array($handle)) {
                foreach ($handle as $key => $val) {
                    unset($handle[$key]);
                    $key          = self::encodeFromJSON($key);
                    $handle[$key] = self::encodeFromJSON($val);
                }
            } elseif (is_object($handle)){
                $arCorresponds = array();
                foreach($handle as $key => $val){
                    $arCorresponds[$key] = array(self::encodeFromJSON($key),self::encodeFromJSON($val));
                }
                foreach($arCorresponds as $key => $new)
                {
                    unset($handle->$key);
                    $handle->$new[0] = $new[1];
                }
            } else {
                $handle = $GLOBALS['APPLICATION']->ConvertCharset($handle, 'UTF-8', LANG_CHARSET);
            }
        }
        return $handle;
    }


    /**
     * @param float $val
     * @param int $mode
     * @return float
     * Makes price (2 symbols after comma). Mode: 1 - ceil, 2 - round, 3 - floor
     */
    public static function floatToPrice($val, $mode=1){
        $val *= 100;
        switch($mode){
            case 2  : $val = round($val); break;
            case 3  : $val = floor($val); break;
            default : $val = ceil($val);  break;
        }

        return $val / 100;
    }

    static function getCommonCss(){?>
        <style>
            .<?=self::$MODULE_LBL?>errInput{
                background-color: #ffb3b3 !important;
            }
            .<?=self::$MODULE_LBL?>PropHint, .<?=self::$MODULE_LBL?>PropHint:hover{
                background: url("/bitrix/js/main/core/images/hint.gif") no-repeat transparent !important;
                text-decoration: none !important;
                display: inline-block;
                height: 12px;
                position: relative;
                width: 12px;
            }
            .<?=self::$MODULE_LBL?>b-popup {
                background-color: #FEFEFE;
                border: 1px solid #9A9B9B;
                box-shadow: 0px 0px 10px #B9B9B9;
                display: none;
                font-size: 12px;
                padding: 19px 13px 15px;
                position: absolute;
                top: 38px;
                width: 300px;
                z-index: 50;
            }
            .<?=self::$MODULE_LBL?>b-popup .<?=self::$MODULE_LBL?>pop-text {
                margin-bottom: 10px;
                color:#000;
            }
            .<?=self::$MODULE_LBL?>pop-text i {color:#AC12B1;}
            .<?=self::$MODULE_LBL?>b-popup .<?=self::$MODULE_LBL?>close {
                background: url("/bitrix/images/<?=self::$MODULE_ID?>/popup_close.gif") no-repeat transparent;
                cursor: pointer;
                height: 10px;
                position: absolute;
                right: 4px;
                top: 4px;
                width: 10px;
            }
            .<?=self::$MODULE_LBL?>warning{
                color:red !important;
            }
            .<?=self::$MODULE_LBL?>hidden {
                display:none !important;
            }
        </style>
    <?}

    // OPTIONS
    static function placeFAQ($code){?>
        <a class="ipol_header" onclick="$(this).next().toggle(); return false;"><?=self::getMessage('FAQ_'.$code.'_TITLE')?></a>
        <div class="ipol_inst"><?=self::getMessage('FAQ_'.$code.'_DESCR')?></div>
    <?}

    static function placeHint($code){?>
        <div id="pop-<?=$code?>" class="<?=self::$MODULE_LBL?>b-popup" style="display: none; ">
            <div class="<?=self::$MODULE_LBL?>pop-text"><?=self::getMessage("HELPER_".$code)?></div>
            <div class="<?=self::$MODULE_LBL?>close" onclick="$(this).closest('.<?=self::$MODULE_LBL?>b-popup').hide();"></div>
        </div>
    <?}

    static function makeSelect($id,$vals,$def=false,$atrs=''){
        $select = "<select ".(($id) ? "name='".((strpos($atrs,'multiple')===false)?$id:$id.'[]')."' id='{$id}' " : '' )." {$atrs}>";
			if(is_array($vals)){
				foreach($vals as $val => $sign)
					$select .= "<option value='{$val}' ".(((is_array($def) && in_array($val,$def)) || $def == $val )?'selected':'').">{$sign}</option>";
			}
        $select .= "</select>";

        return $select;
    }

    /**
     * @param $code
     * makes da heading, FAQ und send command to establish included options
     */
    static function placeOptionBlock($code,$isHidden=false)
    {
        global $arAllOptions;
        ?>
        <tr class="heading"><td colspan="2" valign="top" align="center" <?=($isHidden) ? "class='".self::$MODULE_LBL."headerLink' onclick='".self::$MODULE_LBL."setups.getPage(\"main\").showHidden($(this))'" : ''?>><?=self::getMessage("HDR_".$code)?></td></tr>
        <?if(self::getMessage('FAQ_'.$code.'_TITLE')){?>
            <tr><td colspan="2"><?self::placeFAQ($code)?></td></tr>
        <?}
        if(Logger::getLogInfo($code)){
            self::placeWarningLabel(Logger::toOptions($code),self::getMessage("WARNING_".$code),150,array('name'=>Tools::getMessage('LBL_CLEAR'),'action'=>self::$MODULE_LBL.'setups.getPage("main").clearLog("'.$code.'")','id'=>'clear'.$code));
        }
        ShowParamsHTMLByArray($arAllOptions[$code],$isHidden);
    }

    /**
     * @param $name
     * @param $val
     * Draws tr-td. That's all. Bwahahahaha.
     */
    static function placeOptionRow($name, $val){
        if($name){?>
            <tr>
                <td width='50%' class='adm-detail-content-cell-l'><?=$name?></td>
                <td width='50%' class='adm-detail-content-cell-r'><?=$val?></td>
            </tr>
        <?}else{?>
            <tr><td colspan = '2' style='text-align: center'><?=$val?></td></tr>
        <?}?>
    <?}

    static function defaultOptionPath()
    {
        return "/bitrix/modules/".self::$MODULE_ID."/optionsInclude/";
    }

    static function placeErrorLabel($content,$header=false)
    {?>
        <tr><td colspan='2'>
            <div class="adm-info-message-wrap adm-info-message-red">
                <div class="adm-info-message">
                    <?if($header){?><div class="adm-info-message-title"><?=$header?></div><?}?>
                    <?=$content?>
                    <div class="adm-info-message-icon"></div>
                </div>
            </div>
        </td></tr>
    <?}

    static function placeWarningLabel($content,$header=false,$heghtLimit=false,$click=false)
    {?>
        <tr><td colspan='2'>
            <div class="adm-info-message-wrap">
                <div class="adm-info-message" style='color: #000000'>
                    <?if($header){?><div class="adm-info-message-title"><?=$header?></div><?}?>
                    <?if($click){?><input type="button" <?=($click['id'] ? 'id="'.self::$MODULE_LBL.$click['id'].'"' : '')?> onclick='<?=$click['action']?>' value="<?=$click['name']?>"/><?}?>
                        <div <?if($heghtLimit){?>style="max-height: <?=$heghtLimit?>px; overflow: auto;"<?}?>>
                        <?=$content?>
                    </div>
                </div>
            </div>
        </td></tr>
    <?}

    // STUFF

    public static function getDeliveryIdHref($deliveryId){
        return "/bitrix/admin/sale_delivery_service_edit.php?PARENT_ID=0&ID={$deliveryId}";
    }

    public static function getProfileIdHref($profile_id,$deliveryId){
        return "/bitrix/admin/sale_delivery_service_edit.php?PARENT_ID={$deliveryId}&ID={$profile_id}";
    }

    public static function getOrderLink($id){
        return "/bitrix/admin/sale_order_view.php?ID={$id}";
    }

    public static function getShipmentLink($shipmentId,$orderId){
        return "/bitrix/admin/sale_order_shipment_edit.php?order_id={$orderId}&shipment_id={$shipmentId}";
    }

    public static function isConverted()
    {
        return (\COption::GetOptionString("main","~sale_converted_15",'N') == 'Y');
    }

    public static function getOrderIdFromURL(){
        $arResult = array('ORDER_ID' => false,'SHIPMENT_ID' => false,'MODE' => false);
        if(defined('ADMIN_SECTION') || ADMIN_SECTION === false) {
            $check = ($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['REQUEST_URI'];
            if(
                strpos($check, "/bitrix/admin/sale_order_detail.php") !== false ||
                strpos($check, "/bitrix/admin/sale_order_view.php")   !== false
            ) {
                $arResult['MODE']     = 'order';
                $arResult['ORDER_ID'] = $_REQUEST['ID'];
            }elseif(strpos($_SERVER['PHP_SELF'], "/bitrix/admin/sale_order_shipment_edit.php") !== false){
                $arResult['MODE']        = 'shipment';
                $arResult['ORDER_ID']    = $_REQUEST['order_id'];
                $arResult['SHIPMENT_ID'] = $_REQUEST['shipment_id'];
            }
        }

        return $arResult;
    }
}