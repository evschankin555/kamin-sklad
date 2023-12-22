<?php

namespace AdwMinified;

class Base {
    public static function getSiteId () {
		$siteID = SITE_ID;
		$bAdminSection = self::isAdminSection();
		if ($bAdminSection) {
	        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/mainpage.php');
	        $siteID = (new \CMainPage)->GetSiteByHost();
	        if (!$siteID) {
	            $siteID = 's1';
            }
        }
        return $siteID;
    }
    
    public static function isPageSpeed () {
        return (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') !== false);
    }
    
    public static function isAdminSection () {
        return (defined('ADMIN_SECTION') && ADMIN_SECTION === true);
    }
    
    public static function isPublic () {
        return (defined('SITE_ID') && SITE_TEMPLATE_ID !== 'landing24' && !self::isAdminSection());
    }
    
    public static function optimize (&$content) {
        if (self::isPublic() && self::neadOptimize()) {
            $start = microtime(true);
            $replace = [
                'find' => [],
                'replace' => []
            ];
            $head = self::findHead($content);
            if (HTML::isHtml($head)) {
                $originalHead = $head;
                $replace = Fonts::optimize($head, $replace);
                $replace = CSS::optimize($head, $replace);
                $replace = JS::optimize($head, $replace);
                $replace = HTML::optimizeHead($head, $replace);
                
                $head = str_replace($replace['find'], $replace['replace'], $originalHead);
                
                $replace = [
                    'find' => [ $originalHead ],
                    'replace' => [ $head ]
                ];
                $replace = Image::optimize($content, $replace);
                $replace = HTML::optimize($content, $replace);
                $content = str_replace($replace['find'], $replace['replace'], $content);
                
                $content = HTML::endMinify($content);
                if ($_GET['am-debug'] === 'y') {
                    $content .= PHP_EOL . '<!--' . round(microtime(true) - $start, 4) . '-->';
                }
            }
        }
    }
    
    private static function neadOptimize () {
        $nead = true;
        $option = \COption::GetOptionString(Tools::moduleID, 'WORK_FOR_GROUP', 'ALL', SITE_ID);
        if ($option === 'NOONE') {
            $nead = false;
        } elseif ($option === 'ADMIN') {
            global $USER;
            if (!$USER->IsAdmin()) {
                $nead = false;
            }
        }
        return $nead;
    }
    
    private static function findHead ($content) {
        $explodeContent = explode('</head>', $content);
        return $explodeContent[0];
    }
}