<?
	namespace Ipolh\MO;
	define('IPOLH_MO', 'ipol.mailorder');
	define('IPOLH_MO_LBL', 'IMO_');
	
	spl_autoload_register(function($className){
        if (strpos($className, __NAMESPACE__) === 0)
        {
            $classPath = implode(DIRECTORY_SEPARATOR, explode('\\', substr($className,9)));

            $filename = __DIR__ . DIRECTORY_SEPARATOR . "classes".DIRECTORY_SEPARATOR."lib" . DIRECTORY_SEPARATOR . $classPath . ".php";

            if (is_readable($filename) && file_exists($filename))
                require_once $filename;
        }
    });

	\Bitrix\Main\Loader::registerAutoLoadClasses(IPOLH_MO, array(
        //General
        'mailorderdriver'     => '/classes/general/mailorderclass.php',
        '\\Ipolh\\MO\\option' => '/classes/general/option.php',
    ));
?>