<?php

namespace AdwMinified;

class JS {
    public static function optimize($content, $replace) {
        global $APPLICATION;
        if (\COption::GetOptionString(Tools::moduleID, 'MINIFIED_JS', '', SITE_ID) == 'Y') {
            $pattern = '/\/bitrix\/cache\/js\/.*js\?[0-9]+/';

            preg_match_all($pattern, $content, $linksCache, PREG_PATTERN_ORDER);

            $links = array();

            foreach ($linksCache[0] as $key) {
                $cut = explode('?', $key);
                $cut_min = explode('.js', $key);
                $shortLink =  $cut_min[0] . '.min.js';
                $replace['find'][] = $key;
                $replace['replace'][] = $shortLink . $cut_min[1];
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $shortLink)
                    || filemtime($_SERVER['DOCUMENT_ROOT'] . $shortLink) < filemtime($_SERVER['DOCUMENT_ROOT'] . $cut[0])) {
                    $links[] = $cut[0];
                }
            }

            foreach ($links as $value) {
                $sourcePath = $_SERVER['DOCUMENT_ROOT'] . $value;
                $link_parts = explode('.', $value);
                $extens = $link_parts[count($link_parts) - 1];
                unset($link_parts[count($link_parts) - 1]);
                $minifiedPath = $_SERVER['DOCUMENT_ROOT'] . implode('.', $link_parts) . '.min.' . $extens;
                
                $jsString = $APPLICATION->GetFileContent($sourcePath);
                if (\COption::GetOptionString(Tools::moduleID, 'MINIFY_JS_TOOLS') == 'MatthiasMullie') {
                    $minifier = new \MatthiasMullie\Minify\JS();
                    $minifier->add($jsString);
                    $miniJs = $minifier->minify();
                } elseif (\COption::GetOptionString(Tools::moduleID, 'MINIFY_JS_TOOLS') == 'PHPWee') {
                    $miniJs = \PHPWee\Minify::js($jsString);
                } elseif (\COption::GetOptionString(Tools::moduleID, 'MINIFY_JS_TOOLS') == 'JSMin') {
                    $miniJs = \JSMin\JSMin::minify($jsString);
                } else {
                    $minifier = new \Patchwork\JSqueeze();
                    $miniJs = $minifier->squeeze(
                        $jsString,
                        true,
                        false,
                        false
                    );
                }

                if ($miniJs !== '' && $miniJs !== null) {
                    file_put_contents($minifiedPath, $miniJs);
                } else {
                    copy($sourcePath, $minifiedPath);
                }
            }
        }
        
        return $replace;
    }
}