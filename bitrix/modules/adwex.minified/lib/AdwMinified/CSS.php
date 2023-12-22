<?php

namespace AdwMinified;

class CSS { 
    public static function optimize ($content, $replace) {
        if (\COption::GetOptionString(Tools::moduleID, 'INLINE_CSS', '', SITE_ID) === 'Y') {
            $replace = self::inline($content, $replace);
        }
        if (\COption::GetOptionString(Tools::moduleID, 'MINIFIED_CSS', '', SITE_ID) == 'Y') {
            $replace = self::minify($content, $replace);
        }
        return $replace;
    }
    
    private static function inline ($content, $replace) {
        $onlySmall = (\COption::GetOptionString(Tools::moduleID, 'INLINE_CSS_SMALL_ONLY', '', SITE_ID) === 'Y');
        $regexp = '<link\s[^>]*href="(\/bitrix[^\" >]*)"([^\>]*)rel="stylesheet"([^\>]*)(\/>|>)';
        preg_match_all("/$regexp/m", $content, $matches, PREG_SET_ORDER);
 
        $linkToLoad = [];
        foreach ($matches as $match) {
            $cut = explode('?', $match[1]);
            if ($onlySmall && filesize($_SERVER['DOCUMENT_ROOT'] . $cut[0]) >= 48000) {
                continue;
            }
            if ($_COOKIE[md5($match[0])] == 'Y') {
                continue;
            }
            $obCache = new \CPHPCache();
            if ($obCache->InitCache(86400, $cut[0], Tools::moduleID)) {
                $cache = $obCache->GetVars();
                $miniCSS = $cache['css'];
            } elseif ($obCache->StartDataCache()) {
                $pathAr = explode('/', $match[1]);
                unset($pathAr[count($pathAr) - 1]);
                $path = implode('/', $pathAr);
                $css = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $cut[0]);
                $newPath = $path . '/';
                $css = preg_replace_callback(
                    '/url\s?\([\'"]?(?![\'"]?data:)([^\'")]*)(["\']?\))/m',
                    function($urls) use ($newPath) {
                        if (strpos($urls[1], '/') !== 0) {
                            return 'url("' . $newPath . $urls[1] . '")' ;
                        }
                        return 'url("' . $urls[1] . '")' ;
                    },
                    $css
                );                
                $miniCSS = self::minifyString($css);
                $obCache->EndDataCache(array('css' => $miniCSS));
            }
            $linkToLoad[] = $match[1];
            $replace['find'][] = $match[0];
            $replace['replace'][] = '<style>' . $miniCSS . '</style>';
            setcookie(md5($match[0]), 'Y', 0, '/');
        }
        
        if (count($linkToLoad) > 0) {
            $loadScript = '<script>'
            . 'var eCachedStyles = ' . json_encode($linkToLoad) . ';var eLoadStyle = function(file) { var _sl = document.createElement("link"); _sl.rel = "stylesheet"; _sl.media = "print"; _sl.href = file; document.head.appendChild(_sl);};'
            . 'setTimeout(function () { for (var i = 0; i < eCachedStyles.length; i++) { eLoadStyle(eCachedStyles[i]); } }, 8000);'
            . '</script>';
            
            $replace['find'][] = '</body>';
            $replace['replace'][] = $loadScript . '</body>';
        }
        return $replace;
    }
    
    private static function minify ($content, $replace) {
        global $APPLICATION;
        if (\COption::GetOptionString(Tools::moduleID, 'INLINE_CSS', '', SITE_ID) === 'Y' &&
            \COption::GetOptionString(Tools::moduleID, 'INLINE_CSS_SMALL_ONLY', '', SITE_ID) !== 'Y') {
            return;
        }
        $pattern = '/\/bitrix\/cache\/css\/.*css\?[0-9]+/';

        preg_match_all($pattern, $content, $linksCache, PREG_PATTERN_ORDER);

        $links = [];
        
        foreach ($linksCache[0] as $key) {
            $cut = explode('?', $key);
            $cut_min = explode('.css', $key);
            $shortLink =  $cut_min[0] . '.min.css';
            $replace['find'][] = $key;
            $replace['replace'][] = $shortLink . $cut_min[1];
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $shortLink)
                || filemtime($_SERVER['DOCUMENT_ROOT'] . $shortLink) < filemtime($_SERVER['DOCUMENT_ROOT'] . $cut[0])) {
                $links[] = $cut[0];
            }
        }

        foreach($links as $value) {
            $sourcePath = $_SERVER['DOCUMENT_ROOT'] . $value;
            $link_parts = explode('.', $value);
            $extens = $link_parts[count($link_parts) - 1];
            unset($link_parts[count($link_parts) - 1]);
            $minifiedPath = $_SERVER['DOCUMENT_ROOT'] . implode('.', $link_parts) . '.min.' . $extens;
            
            $cssString = $APPLICATION->GetFileContent($sourcePath);
            $miniCSS = self::minifyString($cssString);
            file_put_contents($minifiedPath, $miniCSS);
        }
        return $replace;
    }
    
    public static function minifyString ($cssString) {
        if (\COption::GetOptionString(Tools::moduleID, 'MINIFY_CSS_TOOLS') == 'PHPWee') {
            $miniCSS = \PHPWee\Minify::css($cssString);
        } else {
            $minifier = new \MatthiasMullie\Minify\CSS();
            $minifier->add($cssString);
            $miniCSS = $minifier->minify();
        }
        
        if ($miniCSS !== '' && $miniCSS !== null) {
            $miniCSS = str_replace('@font-face{', '@font-face{font-display:swap;', $miniCSS);
        } else {
            $miniCSS = $cssString;
        }
        return $miniCSS;
    }
}