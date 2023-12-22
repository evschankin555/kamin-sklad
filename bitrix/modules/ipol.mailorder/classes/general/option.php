<?
namespace Ipolh\MO;

use Ipolh\MO\Tools;

IncludeModuleLangFile(__FILE__);

class option
{
    // optionsControll
	private static $moduleLink       = IPOLH_MO_LBL;
	private static $moduleId		 = IPOLH_MO;

	private static $pathToFileOption = '/bitrix/tools/';

    public static $ABYSS = array();

    public static function get($option)
    {
        if(self::checkFile($option)){
            if(file_exists($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId."/options/".$option.".opt")){
                $self = file_get_contents($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId."/options/".$option.".opt");
            } else
                $self = self::getDefault($option);
        } else {
            $self = \COption::GetOptionString(self::$moduleId,$option,self::getDefault($option));
        }

        if(
            unserialize($self) &&
            self::checkMultiple($option)
        )
            $self = unserialize($self);

        return $self;
    }

    public static function set($option,$val,$doSerialise = false)
    {
        if($doSerialise){
            $val = serialize($val);
        }

        if(self::checkFile($option)){
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId)){
                mkdir($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId);
            }
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId."/options/")){
                mkdir($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId."/options/");
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'].self::$pathToFileOption.self::$moduleId."/options/".$option.".opt",$val);
        } else {
            \COption::SetOptionString(self::$moduleId,$option,$val);
        }
    }

    public function getDefault($option)
    {
        $opt = self::collection();
        if(array_key_exists($option,$opt))
            return $opt[$option]['default'];
        return false;
    }

    public static function checkMultiple($option)
    {
        $opt = self::collection();
        if(array_key_exists($option,$opt) && array_key_exists('multiple',$opt[$option]))
            return $opt[$option]['multiple'];
        return false;
    }

    public static function checkFile($option)
    {
        $opt = self::collection();
        if(array_key_exists($option,$opt) && array_key_exists('file',$opt[$option]))
            return $opt[$option]['file'];
        return false;

    }

    public static function toOptions($helpMakros = false)
    {
        if(!$helpMakros)
            $helpMakros = "<a href='#' class='".self::$moduleLink."PropHint' onclick='return ".self::$moduleLink."setups.popup(\"pop-#CODE#\", this);'></a>";

        $arOptions = array();
        foreach(self::collection() as $optCode => $optVal){
            if(!array_key_exists('group',$optVal) || !$optVal['group'])
                continue;

            if (!array_key_exists($optVal['group'], $arOptions))
                $arOptions[$optVal['group']] = array();
			
			$optionLabel = (strpos($optCode,'IPOLMO_') === 0) ? substr($optCode,11) : $optCode;

            $name = ($optVal['hasHint'] == 'Y') ? " ".str_replace('#CODE#',$optionLabel,$helpMakros) : '';

            $arDescription = array($optCode,Tools::getMessage("OPT_{$optionLabel}").$name,$optVal['default'],array($optVal['type']));

            $arOptions[$optVal['group']][] = $arDescription;
        }

        return $arOptions;
    }

    public static function collection()
    {
        // name - always IPOLMO_OPT_<code>
        $arOptions = array(
            // main
            'IPOLMO_OPT_WORKMODE' => array(
                'group'   => 'main',
                'hasHint' => 'N',
                'default' => "1",
                'type'    => 'text'
            ),
            'IPOLMO_OPT_TEXTMODE' => array(
                'group'   => 'main',
                'hasHint' => 'N',
                'default' => "1",
                'type'    => 'text'
            ),
            'IPOLMO_OPT_PROPS' => array(
                'group'   => 'main',
                'hasHint' => 'N',
                'default' => "",
                'type'    => 'text'
            ),
            'IPOLMO_OPT_EVENTS' => array(
                'group'   => 'main',
                'hasHint' => 'N',
                'default' => "SALE_NEW_ORDER",
                'type'    => 'text'
            ),
            'IPOLMO_OPT_ADDEVENTS' => array(
                'group'   => 'main',
                'hasHint' => 'N',
                'default' => "a:{}",
                'type'    => 'text'
            ),
			// Additional setups
            'IPOLMO_OPT_LOCATIONSEPARATOR' => array(
                'group'   => 'additional',
                'hasHint' => 'N',
                'default' => ", ",
                'type'    => 'text'
            ),
			// inner
            'IPOLMO_OPT_LOCATIONDETAILS' => array(
                'group'   => 'inner',
                'hasHint' => 'N',
                'default' => \mailorderdriver::getDefLocationTypes(),
                'type'    => 'text'
            ),
        );

        return $arOptions;
    }

    public static function getSelectVals($code)
    {
        $arVals = false;

        switch($code){
			default: break;
        }

        return $arVals;
    }
}