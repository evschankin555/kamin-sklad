<?php

namespace AdwMinified;

class Image {
    public static function optimize ($content, $replace) {
        $imageUrls = [];
        $webpMinify = WebP::neadOptimize();
        $lazyMinify = LazyLoad::neadOptimize();
        if ($webpMinify || $lazyMinify) {
            $imageUrls = self::findInContent($content);
            if ($webpMinify) {
                $imageUrls = WebP::optimize($imageUrls);
            }
            if ($lazyMinify) {
                $imageUrls = LazyLoad::optimize($imageUrls);
            }
        }
        foreach ($imageUrls as $tag => $data) {
            $replace['find'][] = $tag;
            $replace['replace'][] = $data['tag'];
        }
        return $replace;
    } 
    
    public static function find ($limit = 100, $offset = 0, $addPath) {
        try {
            $charset = LANG_CHARSET;
            $i = 0;
            $rdi = new \recursiveDirectoryIterator(
                $_SERVER['DOCUMENT_ROOT'] . $addPath,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS
            );
            $exceptSt = \COption::GetOptionString(Tools::moduleID, 'EXCEPT_FOLDER');
            $filtered = new DirFilter($rdi, explode(',', $exceptSt));
            $ffiltered = new \recursiveIteratorIterator($filtered);
            $it = new \RegexIterator($ffiltered, '/\.(?:png|PNG|jpg|JPG|jpeg|JPEG)$/');

            $limitIterator = new \LimitIterator($it, $offset, $limit);
            foreach ($limitIterator as $key => $value) {
                $fabsname = $limitIterator->getPathname();
                if ($charset == 'windows-1251') {
                    $fabsname = iconv('UTF-8', 'CP1251', $fabsname);
                }
                $fsname = $fabsname;

                $l = strlen($_SERVER['DOCUMENT_ROOT']);
                $fabsname = strlen($fabsname) > $l ? substr($fabsname, $l) : '/';
                $requestFiles[] = self::prepareData($fsname);
            }
            
            $stop = false;
            $minify = self::minifi($requestFiles);            
            $minify['count'] = count($minify['errors']) + count($minify['success']);
            if (count($minify['errors']) > 0) {
                foreach ($minify['errors'] as $key => $error){
                    $minify['errors'][$key] = $error;
                    if ($error['code'] == 402 || $error['code'] == 401 || $error['code'] == 500) {
                        $stop = false;
                    }
                }
            }

            $minify['status'] = 'ok';
            $minify['stop'] = $stop;
            $minify['economy'] = \COption::GetOptionString(Tools::moduleID, 'WAS_SAVED_IMAGE', 0);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return \Bitrix\Main\Web\Json::encode($minify);
    }

    private static function findInContent ($content) {
        $siteId = Base::getSiteId();
        $images = [];
        // if (\COption::GetOptionString(Tools::moduleID, 'USE_PARSER', 'N', $siteId) === 'Y') {            
            // $images = self::findByParser($content);
        // } else {
            $images = self::findByRegExps($content);
        // }
        return $images;
    }
    
    private static function findByParser ($content) {        
        $images = [];
        $document = new \DiDom\Document();
        $document->loadHtml($content, 4);
        $imgs = $document->find('img');
        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            if (empty($src)) {
                $src = $img->getAttribute('data-src');
            }
            $images[$img->html()] = [
                'tag' => $img->html(),
                'link' => $src
            ];
        }
        return $images;
    }
    
    private static function findByRegExps ($content) {
        $regExps = [];
        
        if (\COption::GetOptionString(Tools::moduleID, 'SEARCH_ALL_IMAGES', 'N', SITE_ID) === 'Y') {
            $regExps[] = '<[^>]*["|\'|(](\/[^>]*\.(?:png|jpg|jpeg|webp|PNG|JPG|JPEG|WEBP))[^>]*>';
        } else {
            $regExps[] = '<img[^>]*src=["|\']([^\"\'>]*)["|\'][^>]*>';
            $regExps[] = 'background[-image]?.*:\s*url\(["|\']*(.+?)["|\']*\)';
        }
        
        // '<img[^>]*src=["|\']([^\"\'>]*)["|\'][^>]*>', // images
        // '<img\s[^>]*data-src="([^\" >]*)"([^\>]*)([^\>]*)(\/>|>)', // lazyload src
        // 'background[-image]?.*:\s*url\(["|\']*(.+?)["|\']*\)', // style background
        // 'data-bg="url\(["|\'|' . preg_quote('&#039;') . '|' . preg_quote('&quot;') . '|' . preg_quote('&apos;') . ']*(.+?)["|\'|' . preg_quote('&#039;') . '|' . preg_quote('&quot;') . '|' . preg_quote('&apos;') . ']*\)', // lazyload bg
        // '<[^>]*["|\'|(](\/[^>]*\.(?:png|jpg|jpeg|PNG|JPG|JPEG))[^>]*>', // links on upload

        $images = [];
        foreach ($regExps as $regExp) {
            $images = array_merge($images, self::findByRegExp($content, $regExp));
        }
        return $images;
    }
    
    private static function findByRegExp ($content, $regExp) {
        $matches = [];
        $tagSrc = [];
        preg_match_all('/' . $regExp . '/im', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (strpos($match[1], '{{') !== false || strpos($match[1], '://') !== false || strpos($match[1], '//') === 0) {
                continue;
            }
            $tagSrc[$match[0]] = [
                'tag' => $match[0],
                'link' => $match[1]
            ];
        }
        return $tagSrc;     
    }

    public static function prepareData ($destinationUrl) {        
        $imageData = array(
            'qltJpg' => \COption::GetOptionString(Tools::moduleID, 'QUALITY_JPG'),
            'qltPng' => \COption::GetOptionString(Tools::moduleID, 'QUALITY_PNG'),
            'image' => $destinationUrl,
        );
        return $imageData;
    }
    
    public static function minifiAgent ($offset = 0, $limit = 1, $count = 0) {
        $addPath = '/images';
        if ($count <= 0) {
            $tmpCount = \Bitrix\Main\Web\Json::decode(self::getFieldsCount($addPath, $size = false, $dirIgnore = array('tmp')));
        }

        if ($tmpCount) {
            $count = $tmpCount['count'];

            self::find($limit, $offset);

            if ($offset >= $count) {
                return '';
            } else {
                $offset += 1;
                return '\\AdwMinified\\Minified::minifiAgent(' . $offset . ', ' . $limit . ', ' . $count . ');';
            }
        } else {
            return '';
        }
    }
    
    public static function minifi ($images) {
        return self::minifiLocal($images);
    }
    
    private static function minifiLocal ($images) {
        $charset = LANG_CHARSET;
        $result = array(
            'status' => false,
            'errors' => array(),
            'success' => array(),
        );
        
        $factory = new \ImageOptimizer\OptimizerFactory();
        $hasImagick = (extension_loaded('imagick') || class_exists('Imagick'));
        $siteId = Base::getSiteId();
        $createWebP = WebP::siteUsing();
        $economy = \COption::GetOptionString(Tools::moduleID, 'WAS_SAVED_IMAGE', 0);
        foreach ($images as $image) {
            $result['status'] = true;
            $qualityJpg = $image['qltJpg'];
            $qualityPng = $image['qltPng'];
            $errorMessage = GetMessage('MINIERROR_MESS_401');
            $oldSize = filesize($image['image']);
            if (self::isValid($image['image'])) {  
                copy($image['image'], $image['image'] . '.factory');
                copy($image['image'], $image['image'] . '.php');
                if ($hasImagick) {
                    copy($image['image'], $image['image'] . '.imagic');
                }            
                $info = getimagesize($image['image']);
                // $optimizer = $factory->get();
                if ($info['mime'] == 'image/jpeg') {
                    // $optimizer = $factory->get('jpeg');
                    if ($hasImagick) {
                        self::imagickOpt($image['image'] . '.imagic', $qualityJpg);
                    }
                    $imageF = imagecreatefromjpeg($image['image'] . '.php');
                    unlink ($image['image'] . '.php');
                    $result['status'] = imagejpeg($imageF, $image['image'] . '.php', $qualityJpg);
                    imagedestroy($imageF);
                } elseif ($info['mime'] == 'image/png') {
                    // $optimizer = $factory->get('png');
                    if ($hasImagick) {
                        self::imagickOpt($image['image'] . '.imagic', $qualityPng);
                    }
                    $imageF = imagecreatefrompng($image['image'] . '.php');
                    imageAlphaBlending($imageF, true);
                    imageSaveAlpha($imageF, true);
                    $qualityPng = 9 - (($qualityPng * 9 ) / 100 );
                    unlink ($image['image'] . '.php');
                    $result['status'] = imagePng($imageF, $image['image'] . '.php', $qualityPng);
                    imagedestroy($imageF);
                }
                // $optimizer->optimize($image['image'] . '.factory');
                if ($hasImagick) {
                    if (filesize($image['image'] . '.imagic') < filesize($image['image'] . '.php')) {
                        copy($image['image'] . '.imagic', $image['image'] . '.php');
                    }
                    unlink ($image['image'] . '.imagic');
                }
                //if (filesize($image['image'] . '.factory') >= filesize($image['image'] . '.php')) {
                    // unlink ($image['image'] . '.factory');
                    if (filesize($image['image'] . '.php') < $oldSize) {
                        copy($image['image'] . '.php', $image['image']);
                    }
                    unlink ($image['image'] . '.php');
                /*} else if ((filesize($image['image'] . '.factory') < filesize($image['image'] . '.php'))){
                    unlink ($image['image'] . '.php');
                    if (filesize($image['image'] . '.factory') < $oldSize) {
                        copy($image['image'] . '.factory', $image['image']);
                    }
                    unlink ($image['image'] . '.factory');
                } else {
                    unlink ($image['image'] . '.factory');
                    unlink ($image['image'] . '.php');
                }*/
                $economy += $oldSize - filesize($image['image']);
            } else {
                $result['status'] = false;
                $errorMessage = 'Image is Corrupted';
            }
            
            if ($result['status']) {
                $result['success'][] = $image['image'];
            } else {
                $result['errors'][] = (['code' => 401, 'file' => $image['image'], 'response' => $errorMessage]);
            }
            if ($createWebP) {
                WebP::create($image['image']);
            }
        }
        \COption::SetOptionString(Tools::moduleID, 'WAS_SAVED_IMAGE', $economy);

        return $result;        
    }
    
    private static function isValid ($path) {
        $valid = true;
        if (extension_loaded('imagick') || class_exists('Imagick')) {
            try {
                $imagick = new \Imagick($path);
                $valid = $imagick->valid();
            } catch (\Exception $e) {
                $valid = false;
            }
        } else {
            $valid = @getimagesize($path);
        }
        return $valid;
    }
    
    public static function imagickOpt ($path, $quality) {
        $imagick = new \Imagick();
        $rawImage = file_get_contents($path);
        $imagick->readImageBlob($rawImage);
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();
        if ($quality > 60) {
            $quality -= 10;
        } else if ($quality < 0) {
            $quality = 0;
        }

        $image_types = getimagesize($path);
        $imagick->setImageCompressionQuality($quality);
        $imagick->thumbnailImage($width, $height);
        if ($image_types[2] === IMAGETYPE_JPEG) {            
            $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $imagick->setImageFormat('jpeg');
            $imagick->setSamplingFactors(array('2x2', '1x1', '1x1'));
            $profiles = $imagick->getImageProfiles('icc', true);
            $imagick->stripImage();
            if (!empty($profiles)) {
                $imagick->profileImage('icc', $profiles['icc']);
            }
            $imagick->setInterlaceScheme(\Imagick::INTERLACE_JPEG);
            $imagick->setColorspace(\Imagick::COLORSPACE_SRGB);
        } else if ($image_types[2] === IMAGETYPE_PNG) {
            $imagick->setImageCompression(\Imagick::COMPRESSION_ZIP);
            $imagick->setImageFormat('png');
        } else if ($image_types[2] === IMAGETYPE_GIF) {
            $imagick->setImageCompression(\Imagick::COMPRESSION_ZIP);
            $imagick->setImageFormat('gif');
        }

        $imagick->stripImage();
        $rawData = $imagick->getImageBlob();
        $imagick->destroy();
        
        file_put_contents($path, $rawData);
    }
    
    public static function getCount ($addPath, $needSize = false) {
        try {
            $result['count'] = 0;
            $result['size_out'] = $result['size_in'] = 0;

            $rdi = new \recursiveDirectoryIterator($_SERVER['DOCUMENT_ROOT'] . $addPath,  \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
            $exceptSt = \COption::GetOptionString(Tools::moduleID, 'EXCEPT_FOLDER');
            $filtered = new DirFilter($rdi, explode(',', $exceptSt));
            $ffiltered = new \recursiveIteratorIterator($filtered);
            $it = new \RegexIterator($ffiltered,'/\.(?:png|PNG|jpg|JPG|jpeg|JPEG)$/');

            if ($needSize) {
                foreach ($it as $key => $value) {
                    $result['count'] ++;
                    $result['size_in'] += $value->getSize();
                }
            } else {
                foreach ($it as $key => $value) {
                    $result['count'] ++;
                }
            }

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if ($needSize) {
            $result['size_out'] = Tools::FileSizeConvert($result['size_in'] * 0.7);
            $result['size_in'] = Tools::FileSizeConvert($result['size_in']);
        }

        if ($result['count'] <= 0) {
            $result = array('error' => array('code' => 404, 'message' => GetMessage('MINIERROR_MESS_404')));
        }

        return \Bitrix\Main\Web\Json::encode($result);
    }
    
    public static function minifiById($intFileID) {
        global $DB;
        $result = array('status' => false);
        if (!$intFileID) {
            return null;
        }
        $arFile  = \CFile::GetByID($intFileID)->GetNext();
        if (!$arFile) {
            $z = $DB->Query('SELECT * FROM b_file ORDER BY ID desc LIMIT 10');
            $lastFile = $z->GetNext();
            $intFileID = $lastFile['ID'];
            $arFile = \CFile::GetByID($intFileID)->GetNext();
        }

        $strFilePath = $_SERVER['DOCUMENT_ROOT'] . \CFile::GetPath($intFileID);
        $type = mime_content_type($strFilePath);
        
        if ($type != 'image/jpeg' && $type != 'image/png') {
            return null;
        }

        if (file_exists($strFilePath)) {
            $image = self::prepareData($strFilePath);
            $result = self::minifi([$image]);
            
            if ($result['status']) {
                clearstatcache(true, $strFilePath);
                $newSize = filesize($strFilePath);
                $DB->Query("UPDATE b_file SET FILE_SIZE='" . $DB->ForSql($newSize, 255) . "' WHERE ID=" . intval($intFileID));
            }
        }
        return $result;
    }

    public static function minifiOnElementEvent (&$arFields) {
        if (!\COption::GetOptionString(Tools::moduleID,'MINIFY_LOADELEMNT') == 'Y') {
            return;
        }
        if (is_array($arFields['PREVIEW_PICTURE'])){
            if (intval($arFields['PREVIEW_PICTURE']['old_file']) > 0) {
                self::minifiById($arFields['PREVIEW_PICTURE']['old_file']);
            } else if (!empty($arFields['PREVIEW_PICTURE']['tmp_name'])) {
                $image = self::prepareData($arFields['PREVIEW_PICTURE']['tmp_name']);
                $result = self::minifi([$image]);
            }
        }
        if (is_array($arFields['DETAIL_PICTURE'])){
            if (intval($arFields['DETAIL_PICTURE']['old_file']) > 0) {
                self::minifiById($arFields['DETAIL_PICTURE']['old_file']);
            } else if (!empty($arFields['DETAIL_PICTURE']['tmp_name'])) {
                $image = self::prepareData($arFields['DETAIL_PICTURE']['tmp_name']);
                $result = self::minifi([$image]);
            }
        }
        $arEl = false;

        if ($arFields['PROPERTY_VALUES']) {
            foreach ($arFields['PROPERTY_VALUES'] as $key => $values) {
                foreach ($values as $k => $v) {
                    if ($v['VALUE']['type'] == 'image/png' || $v['VALUE']['type'] == 'image/jpeg') {
                        if (!$arEl) {
                            $rsEl = \CIBlockElement::GetByID($arFields['ID']);
                            if ($obEl = $rsEl->GetNextElement()) {
                                $arEl = $obEl->GetFields();
                                $arEl['PROPERTIES'] = $obEl->GetProperties();
                            }
                        }
                        foreach ($arEl['PROPERTIES'] as $strPropCode => $arProp) {
                            if ($arProp['ID'] == $key) {
                                if ($arProp['MULTIPLE'] != 'N') {
                                    foreach ($arProp['VALUE'] as $intFileID) {
                                        self::minifiById($intFileID);
                                    }
                                } else {
                                    self::minifiById($arProp['VALUE']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function minifiOnSectionEvent (&$arFields) {
        if (\COption::GetOptionString(Tools::moduleID,'MINIFY_LOADSECTION') == 'Y' && $arFields['PICTURE']) {
            $rsSection = \CIBlockSection::GetByID($arFields["ID"]);
            $arSection = $rsSection->GetNext();
            self::minifiById($arSection['PICTURE']);
        }
    }

    public static function minifiOnFileEvent (&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt) {
        if (!\COption::GetOptionString(Tools::moduleID,'MINIFY_ADDINFILETABLE') == 'Y') {
            return;
        }
        if ((!isset($arFile['MODULE_ID']) || $arFile['MODULE_ID'] != 'iblock')){
            if ($arFile['type'] == 'image/jpeg' || $arFile['type'] == 'image/png') {
                $image = self::prepareData($arFile['tmp_name']);
                $result = self::minifi([$image]);
                if ($result['success']) {
                    $arFile['size'] = filesize($arFile['tmp_name']);
                }
            }
        }
    }

    public static function minifiOnResizeEvent(
        $arFile,
        $arParams,
        &$callbackData,
        &$cacheImageFile,
        &$cacheImageFileTmp,
        &$arImageSize
    ) {
        if (!\COption::GetOptionString(Tools::moduleID,'MINIFY_RESIZE') == 'Y') {
            return;
        }
        if ($arFile['CONTENT_TYPE'] == 'image/jpeg' || $arFile['CONTENT_TYPE'] == 'image/png') {
            $image = self::prepareData($cacheImageFileTmp);
            $result = self::minifi([$image]);
        }
    }

    public static function savePosition ($directory = '', $position, $mode = 0) {
        \COption::SetOptionString(Tools::moduleID, 'MINIFY_DIRECTORY', $directory);
        \COption::SetOptionString(Tools::moduleID, 'MINIFY_POSITION', $position);
        \COption::SetOptionString(Tools::moduleID, 'MINIFY_MODE', $mode);
    }

    public static function getPosition ($directory = '', $position = 0, $mode = 0) {
        return array(
            'DIRECTORY' => \COption::GetOptionString(Tools::moduleID, 'MINIFY_DIRECTORY', $directory),
            'POSITION' => \COption::GetOptionString(Tools::moduleID, 'MINIFY_POSITION', $position),
            'MODE' => \COption::GetOptionString(Tools::moduleID, 'MINIFY_MODE', $mode)
        );
    }
}