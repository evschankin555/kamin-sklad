<?php

namespace AdwMinified;

class HTML {
    const additionalStyles = [
        '/bitrix/panel/main/popup',
        '/bitrix/cache/css/s1/aspro_next/kernel_main',
        '/bitrix/js/fileman/html_editor/',
        '/bitrix/js/ui/buttons/ui.buttons',
        '/bitrix/js/main/loader/loader',
        '/bitrix/js/main/core/',
        '/bitrix/js/main/core/css/core_viewer',
        '/bitrix/js/main/core/css/core_finder'
    ];
    
    public static function optimizeHead ($content, $replace) {
        $replace = self::preconnect($content, $replace);
        if (Base::isPageSpeed()) {
            $replace = self::removeStyles($content, $replace);
        } else {
            $replace = self::bitrixLazy($content, $replace);
        }
        return $replace;
    }
    
    public static function optimize ($content, $replace) {
        if (Base::isPageSpeed()) {
            $replace = self::removeIframe($content, $replace);
        }
        return $replace;
    }
    
    public static function endMinify ($content) {
        if (\COption::GetOptionString(Tools::moduleID, 'MINIFIED_HTML', '', SITE_ID) == 'Y') {
            $content = self::minify($content);
        }
        return $content;
    }
    
    private static function removeIframe ($content, $replace) {
        preg_match_all('(<iframe[^>]*><\/iframe>)', $content, $iframes, PREG_PATTERN_ORDER);
        foreach ($iframes as $iframe) {
            $replace['find'][] = $iframe[0];
            $replace['replace'][] = '';
        }
        return $replace;
    }
    
    private static function removeStyles ($content, $replace) {
        foreach(self::additionalStyles as $link) {
            $replace['find'][] = '<link href="' . $link;
            $replace['replace'][] = '<link data-href="' . $link;
        }
        return $replace;
    }
    
    private static function bitrixLazy ($content, $replace) {
        foreach(self::additionalStyles as $link) {
            $replace['find'][] = '<link href="' . $link;
            $replace['replace'][] = '<link media="print" onload="this.media=\'all\'" href="' . $link;
        }
        return $replace;
    }
    
    private static function preconnect ($content, $replace) {
        $links = [ 'https://bitrix.info' ];
        if (\COption::GetOptionString(Tools::moduleID, 'ADD_PRECONNECT_ANALITICS', 'N', SITE_ID) === 'Y') {
            $links[] = 'https://www.google-analytics.com';
        }
        if (\COption::GetOptionString(Tools::moduleID, 'ADD_PRECONNECT_TAG_MANGER', 'N', SITE_ID) === 'Y') {
            $links[] = 'https://www.googletagmanager.com';
        }
        if (\COption::GetOptionString(Tools::moduleID, 'ADD_PRECONNECT_METRICA', 'N', SITE_ID) === 'Y') {
            $links[] = 'https://mc.yandex.ru';
        }
        if (\COption::GetOptionString(Tools::moduleID, 'ADD_PRECONNECT_FACEBOOK', 'N', SITE_ID) === 'Y') {
            $links[] = 'https://connect.facebook.net';
        }
        if (\COption::GetOptionString(Tools::moduleID, 'ADD_PRECONNECT_JIVOSITE', 'N', SITE_ID) === 'Y') {
            $links[] = 'https://code.jivosite.com';
        }
        $htmlPreconnect = '';
        foreach ($links as $link) {
            $htmlPreconnect .= '<link rel="preconnect" href="' . $link . '">';
        }
        $replace['find'][] = '</head>';
        $replace['replace'][] = $htmlPreconnect . '</head>';
        return $replace;
    }
    
    public static function isHtml ($string) {
        $isHtml = true;
        if ( strpos($string, '<!DOCTYPE') === false && strpos($string, '<!doctype') === false ) { // Is not Html
            $isHtml = false;
        }
        return $isHtml;
    }
    
    private static function minify ($content) {
        $canMinify = true;
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        if ($request->isAjaxRequest()) {
            $canMinify = false;
        }
        if ($canMinify) {
            $key = md5($content);
            $html = $content;
            $obCache = new \CPHPCache();
            if ($obCache->InitCache(86400, $key, Tools::moduleID) ) {
                $cache = $obCache->GetVars();
                $html = $cache['html'];
            } elseif( $obCache->StartDataCache()) {
                if (\COption::GetOptionString(Tools::moduleID, 'MINIFY_HTML_TOOLS') == 'PHPWee') {
                    $miniHtml = \PHPWee\Minify::html($content);
                } elseif (\COption::GetOptionString(Tools::moduleID, 'MINIFY_HTML_TOOLS') == 'Shaun') {
                    $miniHtml = \Shaun\Minify::html($content);
                } else {
                    $miniHtml = TinyMinify::html($content);
                    $miniHtml = str_replace('< <!DOCTYPE html>', '<!DOCTYPE html>', $miniHtml);
                }
                if ($miniHtml !== '' && $miniHtml !== null) {
                    $html = $miniHtml;
                    unset($miniHtml);
                }
                $obCache->EndDataCache(array('html' => $html));
            }
            $content = $html;
        }
        return $content;
    }
}