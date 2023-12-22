<?php

namespace AdwMinified;

use WebPConvert\WebPConvert;

class WebP {
    static $defaultOption = [
        'ewww-skip' => true,
        'wpc-skip' => true,            
        'png' => [
            'encoding' => 'auto',
            'near-lossless' => 60,
        ],
        'jpeg' => [
            'encoding' => 'auto',
            'quality' => 'auto',
            'default-quality' => 75,
        ]
    ];
    
    public static function neadOptimize () {
        if (!self::clientSupport()
            || !self::serverSupport()
            || !self::siteUsing()
        ) {
            return false;
        }
        return true;
    }
    
    public static function optimize ($imageUrls) {
        $imageUrls = self::replaceByWebP($imageUrls);
        return $imageUrls;
    }
    
    public static function create ($imagePath, $forced = false, $webpPath = null) {
        $type = mime_content_type($imagePath);
        
        if ($type != 'image/jpeg' && $type != 'image/png') {
            return $imagePath;
        }
        
        if ($webpPath === null) {
            $webpPath = self::getPath($imagePath);
        }        
        if (file_exists($webpPath) && !$forced) {
            return $webpPath;
        }
        
        if ($type == 'image/png') {
            $convertPng = self::getPngConvertType();
            switch ($convertPng) {
                case 'instant':
                    break;
                case 'convert':
                    $originalPath = $imagePath;
                    $imagePath = $originalPath . '.jpg';
                    $image = imagecreatefrompng($originalPath);
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                    imagealphablending($bg, true);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagedestroy($image);
                    imagejpeg($bg, $imagePath, 100);
                    imagedestroy($bg);
                    break;
                case 'skip':
                default:
                    return $imagePath;
                    break;
            }
        }
        
        $options = self::getConvertOptions();
        
        try {
            WebPConvert::convert($imagePath, $webpPath, $options);
            if (isset($originalPath)) {
                unlink($imagePath);
            }
        } catch (\Exception $e) {
            return $imagePath;
        }
        
        return $webpPath;
    }
    
    public static function replaceByWebP ($imageUrls) {
        global $APPLICATION;
        $currDir = dirname($APPLICATION->GetCurPage(true));
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        $forced = (isset($_REQUEST['update_webp']) && $_REQUEST['update_webp'] === 'Y');
        $skipSrc = [];
        foreach ($imageUrls as $tag => $data) {
            if (strpos($tag, 'data-amwebp-skip') !== false || in_array($data['link'], $skipSrc)) {
                $skipSrc[] = $data['link'];
                continue;
            }
            $absPath = $docRoot . Rel2Abs($currDir, $data['link']);
            $webpAbsLink = self::create($absPath, $forced);
            $webpLink = str_replace($docRoot, '', $webpAbsLink);
            $imageUrls[$tag]['tag'] = str_replace($data['link'], $webpLink, $tag);
            $imageUrls[$tag]['link'] = $webpLink;
        }
        return $imageUrls;
    }
    
    public static function getPath ($imagePath) {        
        $pathHash = md5($imagePath);
        $quality = self::getQuality();
        $baseModulePath = Tools::getBasePath();
        $directory = $baseModulePath . '/webp/' . substr($pathHash, 0, 3) . '/' . $quality . '/';
        \CheckDirPath($directory);
        return $directory . $pathHash . '.webp';
    }
    
    public static function getTestDir () {
        $baseModulePath = Tools::getBasePath();
        $directory = $baseModulePath . '/test/';
        \CheckDirPath($directory);
        return $directory;
    }
    
	protected static function getConvertOptions () {
        $options = self::$defaultOption;
        $quality = self::getQuality();
        $options['png']['quality'] = $quality;
        $options['jpeg']['max-quality'] = $quality;
        return $options;
    }
    
	protected static function getPngConvertType () {
		return \COption::GetOptionString(Tools::moduleID, 'CONVERT_PNG_TYPE', 'skip');
	}
    
	protected static function getQuality () {
		return intval(\COption::GetOptionString(Tools::moduleID, 'QUALITY_WEBP', 90));
	}
    
    public static function siteUsing () {
        return (\COption::GetOptionString(Tools::moduleID, 'CREATE_WEBP', 'Y', SITE_ID) === 'Y');
    }
    
	protected static function clientSupport () {
		if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
			return true;
		}
        
		$firefoxVersionPos = strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox/');
		if ($firefoxVersionPos !== false) {
			$firefoxVersion = intval(substr($_SERVER['HTTP_USER_AGENT'], $firefoxVersionPos + 8, 3));
			if ($firefoxVersion >= 65) {
				return true;
			}
		}
		return false;
	}
    
    public static function clearFolder () {
        $basePath = Tools::getBasePath();
        $dir = new \Bitrix\Main\IO\Directory($basePath . '/webp/');
        if ($dir->isExists()) {
            $dir->delete();
        }
    }
    
    public static function serverPngTest () {
        $fileName = 'img.png';
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . Tools::moduleID . '/test/' . $fileName;
        $testDir = self::getTestDir();
        copy($imagePath, $testDir . $fileName);
        $testImage = $testDir . $fileName;
        \COption::SetOptionString(Tools::moduleID, 'OPTIONS_TEST_PNG_WEBP_EXAMPLE_ORG', filesize($testImage));
        $webpPath = $testImage . '.webp';
        try {
            $convertPngOriginal = self::getPngConvertType();
            \COption::SetOptionString(Tools::moduleID, 'CONVERT_PNG_TYPE', 'instant');
            $testWi = self::create($testImage, true, $testImage . '.i.webp');
            \COption::SetOptionString(Tools::moduleID, 'OPTIONS_TEST_PNG_WEBP_EXAMPLE_WI', filesize($testWi));
            \COption::SetOptionString(Tools::moduleID, 'CONVERT_PNG_TYPE', 'convert');
            $testWc = self::create($testImage, true, $testImage . '.c.webp');
            \COption::SetOptionString(Tools::moduleID, 'OPTIONS_TEST_PNG_WEBP_EXAMPLE_WC', filesize($testWc));
            \COption::SetOptionString(Tools::moduleID, 'CONVERT_PNG_TYPE', $convertPngOriginal);
        } catch (\Exception $e) {
            self::serverSupportSetFalse();
        }
    }
    
    public static function serverSupport () {
        $option = \COption::GetOptionString(Tools::moduleID, 'CAN_CREATE_WEBP', 'FIRST');
        if ($option === 'FIRST') {
            return self::serverSupportTest();
        }
        return ($option === 'Y');
    }
    
    public static function serverSupportTest () {
        $options = self::getConvertOptions();
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . Tools::moduleID . '/test/img.png';
        $webpPath = $imagePath . '.webp';
        try {
            WebPConvert::convert($imagePath, $webpPath, $options);
            self::serverSupportSetTrue();
            return true;
        } catch (\Exception $e) {
            self::serverSupportSetFalse();
            return false;
        }
    }
    
    protected static function serverSupportSetTrue () {
        self::serverPngTest();
        \COption::SetOptionString(Tools::moduleID, 'CAN_CREATE_WEBP', 'Y');
    }
    
    protected static function serverSupportSetFalse () {
        \COption::SetOptionString(Tools::moduleID, 'CAN_CREATE_WEBP', 'N');
    }
}