<?
namespace Ipolh\MO;

use Ipolh\MO\Tools;

class Logger
{
    public static function toLog($wat,$label=false,$src = false,$flags = false){
        if(!$flags){
            $flags = array('ADMIN' => false, 'APPEND' => false);
        }

        if(!$flags['ADMIN'] || Tools::isAdmin()) {
            if ($src) {
                $data = "\n" . date('H:i:s d.m.Y') . "\n";
                if($label){
                    $data .= $label.": ";
                }
                $data .= $wat."\n";
                file_put_contents(self::getFileName($src),  $data, FILE_APPEND);
            } else {
                self::toLogFile($wat,$label,$flags);
            }
        }
    }

    public static function getLogInfo($src){
        if(
            !self::checkSrc(true) ||
            !file_exists(self::getFileName($src))
        ){
            return '';
        } else {
            return file_get_contents(self::getFileName($src));
        }
    }

    public static function clearLog($src){
        if(
            self::checkSrc(true) ||
            file_exists(self::getFileName($src))
        ) {
            unlink(self::getFileName($src));
        }
    }

    protected static function checkSrc($noCreate = false){
        $exist = file_exists(self::getRootPath());
        if(!$exist && !$noCreate){
            mkdir(self::getRootPath());
        }
        return $exist;
    }

    protected static function getFileName($src = false){
        if(!$src){
            return $_SERVER['DOCUMENT_ROOT']."/MOLog.txt";
        } else {
            self::checkSrc();
            return self::getRootPath()."/".$src.".txt";
        }
    }

    protected static function getRootPath()
    {
        return $_SERVER['DOCUMENT_ROOT']."/".Tools::getJSPath().'logs';
    }

    // simpleLog

    protected static $fileLink = false;

    protected static function toLogFile($wat,$label=false,$flags=array('APPEND'=>false)){
        if(!self::$fileLink){
            self::$fileLink = fopen(self::getFileName(),($flags['APPEND']) ? 'a' : 'w');
            fwrite(self::$fileLink,"\n\n".date('H:i:s d.m.Y')."\n");
        }
        $label = ($label) ? $label.": " : '';
        fwrite(self::$fileLink,$label.print_r($wat,true)."\n");
    }

    // toOptions

    public static function toOptions($src)
    {
        $strInfo   = self::getLogInfo($src);
        $strReturn = '';

        if($strInfo){
            $arInfo = explode("\n\n",$strInfo);
            rsort($arInfo);
            foreach ($arInfo as $text){
                if($text){
                    $strReturn .= str_replace("\n","<br>",$text)."<br>";
                }
            }
        }

        return $strReturn;
    }
}