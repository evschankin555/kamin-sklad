<?php

namespace AdwMinified;

class Fonts {
    public static function optimize ($content, $replace) {
        if (Base::isPublic()) {
            if (\COption::GetOptionString(Tools::moduleID, 'DELETE_BITRIX_OPENSANS', 'Y', SITE_ID) === 'Y') {
                $replace = self::deleteBitrixOpenSans($content, $replace);
            }
            if (\COption::GetOptionString(Tools::moduleID, 'OPTIMIZE_GFONTS', 'Y', SITE_ID) === 'Y') {
                $replace = self::optimizeGFont($content, $replace);
            }
        }
        return $replace;
    }
    
    private static function deleteBitrixOpenSans ($content, $replace) {
        $regexp = '<link\s[^>]*href=["\'](\/bitrix\/js\/ui\/fonts\/opensans\/[^\" >]*)["\']([^\>]*)rel=["\']stylesheet["\']([^\>]*)(\/>|>)';
        preg_match_all("/$regexp/m", $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $replace['find'][] = $match[0];
            $replace['replace'][] = '';
        }
        return $replace;
    }
    
    private static function optimizeGFont ($content, $replace) {
        $regexp = '<link\s[^>]*href=["\'](https:\/\/fonts\.googleapis\.com[^\" >]*)["\']([^\>]*)rel=["\']stylesheet["\']([^\>]*)(\/>|>)';
        preg_match_all("/$regexp/m", $content, $matches, PREG_SET_ORDER);
        
        $wasAddPreconnect = false;

        $inlineFontCss = \COption::GetOptionString(Tools::moduleID, 'OPTIMIZE_GFONTS_INLINE', 'N', SITE_ID) === 'Y';
        if ($inlineFontCss) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: {$userAgent}\r\n",
                ],
            ]);
        }
        foreach ($matches as $match) {
            $newLink = '';
            $fontUrl = $match[1];
            if ($wasAddPreconnect === false) {
                $newLink .= '<link rel="dns-prefetch" href="https://fonts.gstatic.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">';
                $wasAddPreconnect = true;
            }
            if ($inlineFontCss) {
                $obCache = new \CPHPCache();
                $fontCss = file_get_contents($fontUrl, false, $context);
                if ($obCache->InitCache(86400, md5($fontCss), Tools::moduleID)) {
                    $cache = $obCache->GetVars();
                    $miniCSS = $cache['css'];
                } elseif ($obCache->StartDataCache()) {
                    $miniCSS = CSS::minifyString($fontCss);
                    $obCache->EndDataCache(array('css' => $miniCSS));
                }
                $newLink .= '<style>' . $miniCSS . '</style>';
            } else {
                $newLink .= '<link rel="preload" href="' . $fontUrl . '" as="fetch" crossorigin="anonymous">';
                $newLink .= '<script data-skip-moving="true" type="text/javascript">!function(e,n,t){"use strict";var o="' . $fontUrl . '",r="__3perf_googleFonts_f354c";function c(e){(n.head||n.body).appendChild(e)}function a(){var e=n.createElement("link");e.href=o,e.rel="stylesheet",c(e)}function f(e){if(!n.getElementById(r)){var t=n.createElement("style");t.id=r,c(t)}n.getElementById(r).innerHTML=e}e.FontFace&&e.FontFace.prototype.hasOwnProperty("display")?(t[r]&&f(t[r]),fetch(o).then(function(e){return e.text()}).then(function(e){return e.replace(/@font-face {/g,"@font-face{font-display:swap;")}).then(function(e){return t[r]=e}).then(f).catch(a)):a()}(window,document,localStorage);</script>';
            }
            $replace['find'][] = $match[0];
            $replace['replace'][] = $newLink;
        }
        return $replace;
    }
}