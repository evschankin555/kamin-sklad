<?php

namespace AdwMinified;

class LazyLoad {
	const className = 'ami-lazy';
	const imgName = '1px.png';
	const jsName = 'lazyload.js';
	const jsDir = '/bitrix/js/adwex.minified/';
    
    public static function neadOptimize () {
        return (\COption::GetOptionString(Tools::moduleID, 'USE_LAZYLOAD', 'N', SITE_ID) === 'Y');
    }    
    
    public function optimize ($imageUrls) {
        foreach ($imageUrls as $tag => $data) {
            if (!self::isImgTag($data['tag'])) {
                continue;
            }
            if (strpos($tag, 'data-amlazy-skip') !== false) {
                continue;
            }
            $imageUrls[$tag]['tag'] = self::addClass($data['tag']);
        }
        $imageUrls = self::addFiles($imageUrls);
        return $imageUrls;
    }
    
    private function isImgTag ($tag) {
        return (strpos($tag, '<img ') !== false);
    }
    
    private function addClass ($tag) {
        $find = [];
        $replace = [];
        if (strpos($tag, 'class="') !== false || strpos($tag, 'class="') !== false) {
            $find[] = 'class="';
            $find[] = 'class=\'';
            $replace[] = 'class="' . self::className . ' ';
            $replace[] = 'class=\'' . self::className . ' ';
        } else {
            $find[] = '<img ';
            $replace[] = '<img class="' . self::className . '" ';
        }
        $find[] = ' src="';
        $find[] = ' src=\'';
        $replace[] = ' src="/bitrix/modules/adwex.minified/js/' . self::imgName . '" data-src="';
        $replace[] = ' src=\'/bitrix/modules/adwex.minified/js/' . self::imgName . '\'  data-src=\'';
        $tag = str_replace($find, $replace, $tag);
        return $tag;
    }
    
    private function addFiles ($imageUrls) {
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        if (!file_exists($docRoot . self::jsDir . self::imgName)) {            
            \CheckDirPath($docRoot . self::jsDir);
            copy($docRoot . '/bitrix/modules/adwex.minified/js/' . self::imgName, $docRoot . self::jsDir . self::imgName);
            copy($docRoot . '/bitrix/modules/adwex.minified/js/' . self::jsName, $docRoot . self::jsDir . self::jsName);
        }
        $imageUrls['</head>'] = ['tag' => '<script async data-skip-moving="true" src="' . self::jsDir . self::jsName . '"></script></head>'];
        return $imageUrls;
    }
}