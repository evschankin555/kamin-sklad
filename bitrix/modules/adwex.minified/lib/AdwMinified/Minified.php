<?php
namespace AdwMinified;
use AdwMinified\Tools;
use AdwMinified\DirFilter;

class Minified {
    public static function preconnect(&$content) {
        if (defined('SITE_ID') && SITE_TEMPLATE_ID !== 'landing24') {
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
            $htmlPreconnect = '';
            foreach ($links as $link) {
                $htmlPreconnect .= '<link rel="preconnect" href="' . $link . '">';
            }
            $content = str_replace('</head>', $htmlPreconnect . '</head>', $content);
        }
    }
    
    public static function fonts(&$content) {
        if (defined('SITE_ID') && SITE_TEMPLATE_ID !== 'landing24') {
            if (\COption::GetOptionString(Tools::moduleID, 'DELETE_BITRIX_OPENSANS', 'Y', SITE_ID) === 'Y') {
                self::deleteBitrixOpenSans($content);
            }
            if (\COption::GetOptionString(Tools::moduleID, 'OPTIMIZE_GFONTS', 'Y', SITE_ID) === 'Y') {
                self::optimizeGFont($content);
            }
        }
    }
    
    public static function deleteBitrixOpenSans (&$content) {
        $regexp = '<link\s[^>]*href=["\'](\/bitrix\/js\/ui\/fonts\/opensans\/[^\" >]*)["\']([^\>]*)rel=["\']stylesheet["\']([^\>]*)(\/>|>)';
        preg_match_all("/$regexp/m", $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $content = str_replace($match[0], '', $content);
        }
    }
    
    public static function optimizeGFont (&$content) {
        $regexp = '<link\s[^>]*href=["\'](https:\/\/fonts\.googleapis\.com[^\" >]*)["\']([^\>]*)rel=["\']stylesheet["\']([^\>]*)(\/>|>)';
        preg_match_all("/$regexp/m", $content, $matches, PREG_SET_ORDER);
        
        $wasAddPreconnect = false;

        $inlineFontCss = \COption::GetOptionString(Tools::moduleID, 'OPTIMIZE_GFONTS_INLINE', 'N', SITE_ID) === 'N';
        if ($inlineFontCss) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: {$userAgent}\r\n",
                ],
            ]);
        }
        foreach($matches as $match) {
            $newLink = '';
            $fontUrl = $match[1];
            if ($wasAddPreconnect === false) {
                $newLink .= '<link rel="dns-prefetch" href="https://fonts.gstatic.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">';
                $wasAddPreconnect = true;
            }
            if ($inlineFontCss) {
                $obCache = new \CPHPCache();
                $fontCss = file_get_contents($fontUrl, false, $context);
                $fontCss = str_replace('@font-face {', '@font-face{font-display:swap;', $fontCss);  
                if ($obCache->InitCache(86400, md5($fontCss), Tools::moduleID)) {
                    $cache = $obCache->GetVars();
                    $miniCSS = $cache['css'];
                } elseif ($obCache->StartDataCache()) {                  
                    if (\COption::GetOptionString(Tools::moduleID, 'MINIFY_CSS_TOOLS') == 'PHPWee') {
                        $miniCSS = \PHPWee\Minify::css($fontCss);
                    } else {
                        $minifier = new \MatthiasMullie\Minify\CSS();
                        $minifier->add($fontCss);
                        $miniCSS = $minifier->minify();
                    }
                    
                    if ($miniCSS === '' || $miniCSS === null) {
                        $miniCSS = $fontCss;
                    }
                    $obCache->EndDataCache(array('css' => $miniCSS));
                }
                $newLink .= '<style>' . $miniCSS . '</style>';
            } else {
                $newLink .= '<link rel="preload" href="' . $fontUrl . '" as="fetch" crossorigin="anonymous">';
                $newLink .= '<script data-skip-moving="true" type="text/javascript">!function(e,n,t){"use strict";var o="' . $fontUrl . '",r="__3perf_googleFonts_f354c";function c(e){(n.head||n.body).appendChild(e)}function a(){var e=n.createElement("link");e.href=o,e.rel="stylesheet",c(e)}function f(e){if(!n.getElementById(r)){var t=n.createElement("style");t.id=r,c(t)}n.getElementById(r).innerHTML=e}e.FontFace&&e.FontFace.prototype.hasOwnProperty("display")?(t[r]&&f(t[r]),fetch(o).then(function(e){return e.text()}).then(function(e){return e.replace(/@font-face {/g,"@font-face{font-display:swap;")}).then(function(e){return t[r]=e}).then(f).catch(a)):a()}(window,document,localStorage);</script>';
            }
            $content = str_replace($match[0], $newLink, $content);
        }
    }
    
    public static function inlineCss(&$content) {
        global $USER;
        if (defined('SITE_ID') && \COption::GetOptionString(Tools::moduleID, 'INLINE_CSS', '', SITE_ID) === 'Y') {
            $onlySmall = (\COption::GetOptionString(Tools::moduleID, 'INLINE_CSS_SMALL_ONLY', '', SITE_ID) === 'Y');
            $regexp = '<link\s[^>]*href="(\/bitrix[^\" >]*)"([^\>]*)rel="stylesheet"([^\>]*)(\/>|>)';
            preg_match_all("/$regexp/m", $content, $matches, PREG_SET_ORDER);
 
            $linkToLoad = [];
            foreach($matches as $match) {
                global $USER;
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
                    if (\COption::GetOptionString(Tools::moduleID, 'MINIFY_CSS_TOOLS') == 'PHPWee') {
                        $miniCSS = \PHPWee\Minify::css($css);
                    } else {
                        $minifier = new \MatthiasMullie\Minify\CSS();
                        $minifier->add($css);
                        $miniCSS = $minifier->minify();
                    }
                    
                    if ($miniCSS === '' || $miniCSS === null) {
                        $miniCSS = $css;
                    }
                    $miniCSS = str_replace('@font-face{', '@font-face{font-display:swap;', $miniCSS);  
                    $obCache->EndDataCache(array('css' => $miniCSS));
                }
                $linkToLoad[] = $match[1];
                $content = str_replace($match[0], '<style>' . $miniCSS . '</style>', $content);
                setcookie(md5($match[0]), 'Y', 0, '/');
            }
        
            if (count($linkToLoad) > 0) {
                $loadScript = '<script>'
                . 'var eCachedStyles = ' . json_encode($linkToLoad) . ';var eLoadStyle = function(file) { var _sl = document.createElement("link"); _sl.rel = "stylesheet"; _sl.media = "print"; _sl.href = file; document.head.appendChild(_sl);};'
                . 'setTimeout(function () { for (var i = 0; i < eCachedStyles.length; i++) { eLoadStyle(eCachedStyles[i]); } }, 8000);'
                . '</script>';
                $content = str_replace('</body>', $loadScript . '</body>', $content);
            }
        }
    }
                
    
    public static function minifiCss(&$content) {
        global $APPLICATION;
        if (defined('SITE_ID') && \COption::GetOptionString(Tools::moduleID, 'MINIFIED_CSS', '', SITE_ID) == 'Y') {
            if (\COption::GetOptionString(Tools::moduleID, 'INLINE_CSS', '', SITE_ID) === 'Y' &&
                \COption::GetOptionString(Tools::moduleID, 'INLINE_CSS_SMALL_ONLY', '', SITE_ID) !== 'Y') {
                return;
            }
            $pattern = '/\/bitrix\/cache\/css\/.*css\?[0-9]+/';

            preg_match_all($pattern, $content, $links_cache, PREG_PATTERN_ORDER);

            $links_min = array();
            $links = array();

            foreach($links_cache[0] as $key) {
                $cut = explode('?', $key);
                $cut_min = explode('.css', $key);
                $shortLink =  $cut_min[0] . '.min.css';
                $links_min[] = $shortLink . $cut_min[1];
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
                if (\COption::GetOptionString(Tools::moduleID, 'MINIFY_CSS_TOOLS') == 'PHPWee') {
                    $miniCSS = \PHPWee\Minify::css($cssString);
                } else {
                    $minifier = new \MatthiasMullie\Minify\CSS();
                    $minifier->add($cssString);
                    $miniCSS = $minifier->minify();
                }
                                  
                if ($miniCSS !== '' && $miniCSS !== null) {
                    $miniCSS = str_replace('@font-face{', '@font-face{font-display:swap;', $miniCSS);
                    file_put_contents($minifiedPath, $miniCSS);
                } else {
                    copy($sourcePath, $minifiedPath);
                }
            }

            $content = str_replace($links_cache[0], $links_min, $content);
        }
    }
    
    public static function minifiJs(&$content) {
        global $APPLICATION;
        if (defined('SITE_ID') && \COption::GetOptionString(Tools::moduleID, 'MINIFIED_JS', '', SITE_ID) == 'Y' && SITE_TEMPLATE_ID !== 'landing24') {
            $pattern = '/\/bitrix\/cache\/js\/.*js\?[0-9]+/';

            preg_match_all($pattern, $content, $links_cache, PREG_PATTERN_ORDER);

            $links_min = array();
            $links = array();

            foreach($links_cache[0] as $key) {
                $cut = explode('?', $key);
                $cut_min = explode('.js', $key);
                $shortLink =  $cut_min[0] . '.min.js';
                $links_min[] = $shortLink . $cut_min[1];
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

            $content = str_replace($links_cache[0], $links_min, $content);
        }
    }
    
    public static function minifiHtml(&$content) {
        $canMinify = true;
        // Is JSON?
        if (is_object(json_decode($content))) {
            $canMinify = false;
        } elseif (stripos($content, '<!DOCTYPE') === false ) { // Is not Html
            $canMinify = false;
        }
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $request = $context->getRequest();
        if ($request->isAjaxRequest()) {
            $canMinify = false;
        }
        if ($canMinify && \COption::GetOptionString(Tools::moduleID, 'MINIFIED_HTML', '', SITE_ID) == 'Y' && defined('SITE_ID')) {
            global $USER;
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
            unset($html);
        }
    }
    
    public static function findImages($limit = 100, $offset = 0, $addPath) {
        try {
            $charset = LANG_CHARSET;
            $i = 0;
            $rdi = new \recursiveDirectoryIterator($_SERVER["DOCUMENT_ROOT"] . $addPath, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
            $exceptSt = \COption::GetOptionString(Tools::moduleID, 'EXCEPT_FOLDER');
            $filtered = new DirFilter($rdi, explode(',', $exceptSt));
            $ffiltered = new \recursiveIteratorIterator($filtered);
            $it = new \RegexIterator($ffiltered, '/\.(?:png|PNG|jpg|JPG|jpeg|JPEG)$/');

            $limitIterator = new \LimitIterator($it, $offset, $limit);
            foreach ($limitIterator as $key => $value) {
                $fabsname = $limitIterator->getPathname();
                if ($charset == 'windows-1251') {
                    $fabsname = iconv("UTF-8", "CP1251", $fabsname);
                }
                $fsname = $fabsname;

                $l = strlen($_SERVER['DOCUMENT_ROOT']);
                $fabsname = strlen($fabsname) > $l ? substr($fabsname, $l) : '/';
                $requestFiles[] = self::prepareImageData($fsname);
            }
            
            $stop = false;
            $minify = self::minifiImage($requestFiles);            
            $minify['count'] = count($minify['errors']) + count($minify['success']);
            if (count($minify['errors']) > 0) {
                foreach ($minify['errors'] as $key => $error){
                    $minify['errors'][$key] = $error;
                    if($error['code'] == 402 || $error['code'] == 401 || $error['code'] == 500) {
                        $stop = false;
                    }
                }
            }

            $minify['status'] = 'ok';
            $minify['stop'] = $stop;
        } catch(Exception $e) {
            echo $e->getMessage();
        }

        return \Bitrix\Main\Web\Json::encode($minify);
    }    

    public static function prepareImageData($destinationUrl) {        
        $imageData = array(
            'qltJpg' => \COption::GetOptionString(Tools::moduleID, 'QUALITY_JPG'),
            'qltPng' => \COption::GetOptionString(Tools::moduleID, 'QUALITY_PNG'),
            'image' => $destinationUrl,
        );
        return $imageData;
    }
    
    public static function minifiImageAgent ($offset = 0, $limit = 1, $count = 0){
        $addPath = '/images';
        if ($count <= 0) {
            $tmp_count = \Bitrix\Main\Web\Json::decode(self::getFieldsCount($addPath, $size = false, $dirIgnore = array('tmp')));
        }

        if ($tmpCount) {
            $count = $tmpCount['count'];

            self::findImages($limit, $offset);

            if ($offset >= $count) {
                return '';
            } else {
                $offset += 1;
                return 'AdwMinified::minifiImageAgent(' . $offset . ', ' . $limit . ', ' . $count . ');';
            }
        } else {
            return '';
        }
    }
    
    public static function minifiImage($images) {
        return self::minifiImageLocal($images);
    }
    
    private static function minifiImageLocal($images) {
        $charset = LANG_CHARSET;
        $result = array(
            'status' => false,
            'errors' => array(),
            'success' => array(),
        );
        
        $factory = new \ImageOptimizer\OptimizerFactory();
        $hasImagick = (extension_loaded('imagick') || class_exists("Imagick"));
        foreach ($images as $image) {
            $result['status'] = true;
            $qualityJpg = $image['qltJpg'];
            $qualityPng = $image['qltPng'];
            $errorMessage = GetMessage('MINIERROR_MESS_401');
            $oldSize = filesize($image['image']);
            if (self::imageIsValid($image['image'])) {  
                copy($image['image'], $image['image'] . '.factory');
                copy($image['image'], $image['image'] . '.php');
                if ($hasImagick) {
                    copy($image['image'], $image['image'] . '.imagic');
                }            
                $info = getimagesize($image['image']);
                $optimizer = $factory->get();
                if ($info['mime'] == 'image/jpeg') {
                    $optimizer = $factory->get('jpeg');
                    if ($hasImagick) {
                        self::imagickOpt($image['image'] . '.imagic', $qualityJpg);
                    }
                    $imageF = imagecreatefromjpeg($image['image'] . '.php');
                    unlink ($image['image'] . '.php');
                    $result['status'] = imagejpeg($imageF, $image['image'] . '.php', $qualityJpg);
                    imagedestroy($imageF);
                } elseif ($info['mime'] == 'image/png') {
                    $optimizer = $factory->get('png');
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
                $optimizer->optimize($image['image'] . '.factory');
                if ($hasImagick) {
                    if (filesize($image['image'] . '.imagic') < filesize($image['image'] . '.php')) {
                        copy($image['image'] . '.imagic', $image['image'] . '.php');
                    }
                    unlink ($image['image'] . '.imagic');
                }
                if (filesize($image['image'] . '.factory') >= filesize($image['image'] . '.php')) {
                    unlink ($image['image'] . '.factory');
                    if (filesize($image['image'] . '.php') < $oldSize) {
                        copy($image['image'] . '.php', $image['image']);
                    }
                    unlink ($image['image'] . '.php');
                } else if ((filesize($image['image'] . '.factory') < filesize($image['image'] . '.php'))){
                    unlink ($image['image'] . '.php');
                    if (filesize($image['image'] . '.factory') < $oldSize) {
                        copy($image['image'] . '.factory', $image['image']);
                    }
                    unlink ($image['image'] . '.factory');
                } else {
                    unlink ($image['image'] . '.factory');
                    unlink ($image['image'] . '.php');
                }
            } else {
                $result['status'] = false;
                $errorMessage = 'Image is Corrupted';
            }
            
            if($result['status']) {
                $result['success'][] = $image['image'];
            } else {
                $result['errors'][] = (array('code' => 401, 'file' => $image['image'], 'response' => $errorMessage));
            }
        }

        return $result;        
    }
    
    private static function imageIsValid($path) {
        $valid = true;
        if (extension_loaded('imagick') || class_exists("Imagick")) {
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
    
    public static function imagickOpt($path, $quality) {
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
            $profiles = $imagick->getImageProfiles("icc", true);
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
        
        file_get_contents($path, $rawData);
    }
    
    public static function getImagesCount($addPath, $needSize = false) {
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
    
    public static function minifiImageByID($intFileID){
        global $DB;
        $result = array('status' => false);
        if(!$intFileID) {
            return null;
        }
        $arFile  = \CFile::GetByID($intFileID)->GetNext();
        if (!$arFile) {
            $z = $DB->Query("SELECT * FROM b_file ORDER BY ID desc LIMIT 10");
            $lastFile = $z->GetNext();
            $intFileID = $lastFile['ID'];
            $arFile = \CFile::GetByID($intFileID)->GetNext();
        }

        $strFilePath = $_SERVER["DOCUMENT_ROOT"] . \CFile::GetPath($intFileID);
        $type = mime_content_type($strFilePath);
        
        if($type != 'image/jpeg' && $type != 'image/png') {
            return null;
        }

        if(file_exists($strFilePath)) {
            $image = self::prepareImageData($strFilePath);
            $result = self::minifiImage(array($image));
            
            if($result['status']) {
                clearstatcache(true, $strFilePath);
                $newSize = filesize($strFilePath);
                $DB->Query("UPDATE b_file SET FILE_SIZE='" . $DB->ForSql($newSize, 255) . "' WHERE ID=" . intval($intFileID));
            }
        }
        return $result;
    }

    public static function minifiImageOnElementEvent(&$arFields) {
        if (!\COption::GetOptionString(Tools::moduleID,'MINIFY_LOADELEMNT') == 'Y') {
            return;
        }
        if (is_array($arFields['PREVIEW_PICTURE'])){
            if (intval($arFields['PREVIEW_PICTURE']['old_file']) > 0) {
                self::minifiImageByID($arFields['PREVIEW_PICTURE']['old_file']);
            } else if (!empty($arFields['PREVIEW_PICTURE']['tmp_name'])) {
                $image = self::prepareImageData($arFields['PREVIEW_PICTURE']['tmp_name']);
                $result = self::minifiImage(array($image));
            }
        }
        if (is_array($arFields['DETAIL_PICTURE'])){
            if (intval($arFields['DETAIL_PICTURE']['old_file']) > 0) {
                self::minifiImageByID($arFields['DETAIL_PICTURE']['old_file']);
            } else if (!empty($arFields['DETAIL_PICTURE']['tmp_name'])) {
                $image = self::prepareImageData($arFields['DETAIL_PICTURE']['tmp_name']);
                $result = self::minifiImage(array($image));
            }
        }
        $arEl = false;

        if($arFields['PROPERTY_VALUES']) {
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
                                        self::minifiImageByID($intFileID);
                                    }
                                } else {
                                    self::minifiImageByID($arProp['VALUE']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function minifiImageOnSectionEvent(&$arFields) {
        if(\COption::GetOptionString(Tools::moduleID,'MINIFY_LOADSECTION') == 'Y' && $arFields['PICTURE']) {
            $rsSection = \CIBlockSection::GetByID($arFields["ID"]);
            $arSection = $rsSection->GetNext();
            self::minifiImageByID($arSection['PICTURE']);
        }
    }

    public static function minifiImageOnFileEvent(&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt) {
        if(!\COption::GetOptionString(Tools::moduleID,'MINIFY_ADDINFILETABLE') == 'Y') {
            return;
        }
        if ((!isset($arFile['MODULE_ID']) || $arFile['MODULE_ID'] != 'iblock')){
            if ($arFile['type'] == 'image/jpeg' || $arFile['type'] == 'image/png') {
                $image = self::prepareImageData($arFile['tmp_name']);
                $result = self::minifiImage(array($image));
                if($result['success']) {
                    $arFile['size'] = filesize($arFile['tmp_name']);
                }
            }
        }
    }

    public static function minifiImageOnResizeEvent(
        $arFile,
        $arParams,
        &$callbackData,
        &$cacheImageFile,
        &$cacheImageFileTmp,
        &$arImageSize
    ) {
        if(!\COption::GetOptionString(Tools::moduleID,'MINIFY_RESIZE') == 'Y') {
            return;
        }
        if ($arFile["CONTENT_TYPE"] == "image/jpeg" || $arFile["CONTENT_TYPE"] == "image/png") {
            $image = self::prepareImageData($cacheImageFileTmp);
            $result = self::minifiImage(array($image));
        }
    }

    public static function savePosition($directory = '', $position, $mode = 0) {
        \COption::SetOptionString(Tools::moduleID, 'MINIFY_DIRECTORY', $directory);
        \COption::SetOptionString(Tools::moduleID, 'MINIFY_POSITION', $position);
        \COption::SetOptionString(Tools::moduleID, 'MINIFY_MODE', $mode);
    }

    public static function getPosition($directory = '', $position = 0, $mode = 0) {
        return array(
            'DIRECTORY' => \COption::GetOptionString(Tools::moduleID, 'MINIFY_DIRECTORY', $directory),
            'POSITION' => \COption::GetOptionString(Tools::moduleID, 'MINIFY_POSITION', $position),
            'MODE' => \COption::GetOptionString(Tools::moduleID, 'MINIFY_MODE', $mode)
        );
    }
}