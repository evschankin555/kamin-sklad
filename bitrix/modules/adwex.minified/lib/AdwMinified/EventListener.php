<?php

namespace AdwMinified;

class EventListener {
    public static function OnBuildGlobalMenu(&$adminMenu, &$moduleMenu) {
        \AdwMinified\Tools::addMenu($adminMenu, $moduleMenu);
    }
    
    public static function OnAfterIBlockElementAdd(&$arFields) {
        \AdwMinified\Image::minifiOnElementEvent($arFields);
    }
    
    public static function OnAfterIBlockElementUpdate(&$arFields) {
        \AdwMinified\Image::minifiOnElementEvent($arFields);
    }
    
    public static function OnAfterIBlockSectionAdd(&$arFields) {
        \AdwMinified\Image::minifiOnSectionEvent($arFields);
    }
    
    public static function OnAfterIBlockSectionUpdate(&$arFields) {
        \AdwMinified\Image::minifiOnSectionEvent($arFields);
    }
    
    public static function OnFileSave(&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt) {
        \AdwMinified\Image::minifiOnFileEvent($arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt);
    }
    
    public static function OnAfterResizeImage($arFile, $arParams, &$callbackData, &$cacheImageFile, &$cacheImageFileTmp, &$arImageSize) {
        \AdwMinified\Image::minifiOnResizeEvent($arFile, $arParams, $callbackData, $cacheImageFile, $cacheImageFileTmp, $arImageSize);
    }
    
    public static function OnEndBufferContent(&$content) {
        \AdwMinified\Base::optimize($content);
        // \AdwMinified\Optimizer::minify($content);
    }
}