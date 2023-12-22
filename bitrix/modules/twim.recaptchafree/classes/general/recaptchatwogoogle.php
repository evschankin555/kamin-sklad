<?
use Bitrix\Main\Page\Asset,
	Bitrix\Main\Application,
    Bitrix\Main\Config\Option, 
    Bitrix\Main\Web\HttpClient,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\SystemException; 

Loc::loadMessages(__FILE__);

class ReCaptchaTwoGoogle{
    /* 
        ReCaptcha 2.0 Google
        modul bitrix
        Shevtcoff S.V. 
        date 24.03.17
        time 12:01
    */ 
    const MODULE = "twim.recaptchafree";
    
	public static function OnAddContentCaptcha(&$content) 
	{	
         
		$arSettings = self::getParamSite();
        if(self::checkActive($arSettings)){
            $theme = isset($arSettings["theme"]) ? $arSettings["theme"] : 'light';
            $size = isset($arSettings["size"]) ? $arSettings["size"] : 'normal';
            $badge = isset($arSettings["badge"]) ? $arSettings["badge"] : 'bottomright';
            
            $content = preg_replace_callback(
                '/<img[^>]+?src\s*=\s*["\']([^"\']*?\/bitrix\/tools\/captcha\.php\?(captcha_code|captcha_sid)=[0-9a-z]+)["\'][^>]*?>/',
                function ($matches) use ($arSettings, $theme, $size, $badge) {
                    $img = $matches[0];
                    $src = $matches[1];
                    $img = str_replace($src, 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==', $img);
                    $img = str_replace(array('style', 'width', 'height'), array('data-style', 'data-width', 'data-height'), $img);
                    $img = str_replace('<img', '<img style="display:none"', $img);
                    $img .= '<div id="recaptcha_'.bin2hex(random_bytes(2)).'" class="g-recaptcha" data-theme="' . $theme . '" data-sitekey="'. $arSettings["key"] .'" data-size="'. $size .'"  data-badge="'. $badge .'" data-callback="RecaptchafreeSubmitForm"></div>';
                    return $img;
                },
                $content
            );
                
            $content = preg_replace_callback(
                '/<input[^>]+?name\s*=[^>]*?captcha_word[^>]+?>/',
                function ($matches) {
                    $input = $matches[0];
                    $input = str_replace(array('style', 'class', 'name="captcha_word"'), array('data-style', 'data-class', 'style="display:none" name="captcha_word"'), $input);
                    return $input;
                },
                $content
            );
            // hide label captcha_word
            if(preg_match('/<[^<]+name="captcha_word"[^>]+>/is', $content, $match) && preg_match('/id="([^"]+)"/is', $match[0], $match)){
                $search = '/for="' . $match[1] . '"/is';
                $replace = 'for="' . $match[1] . '" style="display: none;"';
                $content = preg_replace($search, $replace, $content);
            } 

            // replace msg bx captcha
            $arSearchLabels = array(
                Loc::getMessage('GCT_SEARCH_CAPTCHA_WORD_LABEL_1'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_WORD_LABEL_2'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_WORD_LABEL_3'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_WORD_LABEL_4'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_WORD_LABEL_5'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_WORD_LABEL_6'),
            );
            $content = str_replace($arSearchLabels, Loc::getMessage('GCT_REPLACE_CAPTCHA_WORD'), $content);

            $arSearchTitles = array(
                Loc::getMessage('GCT_SEARCH_CAPTCHA_TITLE_1'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_TITLE_2'),
                Loc::getMessage('GCT_SEARCH_CAPTCHA_TITLE_3')
            );
            $content = str_replace($arSearchTitles, Loc::getMessage('GCT_REPLACE_CAPTCHA_TITLE'), $content);   
        }
	} 
	
	public static function OnVerificContent(){
 		$arSettings = self::getParamSite();
        if(!self::checkActive($arSettings)){return;}
        // add js script in head
        self::setRecaptchaScript($arSettings);
        //check recaptcha
        $context = Application::getInstance()->getContext(); 
        $request = $context->getRequest(); 
        $post = $request->getPostList()->toArray();        
        //request parameters in sub-array.
        foreach ($post as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            $captchaCode = self::checkBxCaptcha($value, $arSettings);
            if (null === $captchaCode) {
                continue;
            }
            if (false === $captchaCode) {
                $captchaCode = 'WRONG_CAPTCHA_CODE';
            }
            $_REQUEST[$key]['captcha_word'] = $_POST[$key]['captcha_word'] = $captchaCode;
            $requestValues = $request->toArray();
            $requestValues[$key]['captcha_word'] = $captchaCode;
            $request->set($requestValues);
            $postValues = $request->getPostList()->toArray();
            $postValues[$key]['captcha_word'] = $captchaCode;
            $request->getPostList()->set($postValues);
        }
        $captchaCode = self::checkBxCaptcha($post, $arSettings);
        if (null === $captchaCode) {
            return;
        }
        if (false === $captchaCode) {
            $captchaCode = 'WRONG_CAPTCHA_CODE';
        }
        $_REQUEST['captcha_word'] = $_POST['captcha_word'] = $captchaCode;
        $requestValues = $request->toArray();
        $requestValues['captcha_word'] = $captchaCode;
        $request->set($requestValues);
        $postValues = $request->getPostList()->toArray();
        $postValues['captcha_word'] = $captchaCode;
        $request->getPostList()->set($postValues);
	}
    /**
     * 
     * @param type $post
     * @param type $arSettings
     * @return boolean
     */
    protected static function checkBxCaptcha($post, $arSettings){
        // No bitrix captcha code
        $captchaSid = $post['captcha_sid'] ? $post['captcha_sid'] : $post['captcha_code'];
        if (!$captchaSid) {
            return null;
        }
        // No reCaptcha code
        $reCaptchaCode = $post['g-recaptcha-response'];
        if (!$reCaptchaCode) {
            return false;
        }
         // ReCaptcha code is wrong
        if (!self::checkReCaptcha($reCaptchaCode, $arSettings)) {
            return false;
        }
        
        $sqlHelper = Application::getConnection()->getSqlHelper();
        $codeQuery = 'SELECT CODE FROM b_captcha WHERE id="' . $sqlHelper->forSql($captchaSid) . '"';
        $queryResult = Application::getConnection()->query($codeQuery)->fetch();
        if ($queryResult && $queryResult['CODE'] && strlen($queryResult['CODE'])) {
            return $queryResult['CODE'];
        }
        return false;
    }
    /**
     * Check Active
     * 
     * @return bool
     */
    private static function checkActive($settings)
    {
        if(defined("ADMIN_SECTION")){
            return false;
        }
        if($settings["act"] != "Y" || !isset($settings["act"])){
            return false;
        }
        if(!($settings["key"] && $settings["secretkey"])){
            return false;
        }
        if(self::checkMask($settings["mask_exclusion"])){
            return false;
        }
        
        return true;
    }
    /**
     * getParamRequest
     * @return $arRequest
     */
	private static function getParamRequest(){
        $arRequest = array();
        $context = Application::getInstance()->getContext(); 
        $request = $context->getRequest(); 
		if($request->isPost()){
            $captcha_sid = $request->getPost("captcha_sid");
            $captcha_code = $request->getPost("captcha_code");
            $arRequest["captcha_sid"] = (!empty($captcha_sid)) ? $captcha_sid : $captcha_code;
            $arRequest["captcha_word"] = $request->getPost("captcha_word");
            $arRequest["g-recaptcha-response"] = $request->getPost("g-recaptcha-response");
        } else{
            $captcha_sid = $request->getQuery("captcha_sid");
            $captcha_code = $request->getQuery("captcha_code");
            $arRequest["captcha_sid"] = (!empty($captcha_sid)) ? $captcha_sid : $captcha_code;
            $arRequest["captcha_word"] = $request->getQuery("captcha_word");
            $arRequest["g-recaptcha-response"] = $request->getQuery("g-recaptcha-response"); 
        }
		return $arRequest;
	}
	/**
     * getParamSite
     * @return array params module
     */
	private static function getParamSite(){
		$settings = Option::get("twim.recaptchafree", "settings", false, SITE_ID);
		return  unserialize($settings);
	}
    /**
     * checkMask
     * @param string $mask_exc 
     * @return boolean
     */
    private static function checkMask($mask_exc){
        $request = Application::getInstance()->getContext()->getServer(); 
        $maskFormat = trim($mask_exc,  " \t\n\r\0\x0B,;:!?");
        if(empty($maskFormat)){ return false; }
        $mask = explode(";", $maskFormat);
		$arMask = array_map(function($n){return trim($n);}, $mask); // trim space in arrat items
        $reg = '%^' . implode('|', $arMask) . '%i'; // set reg
        if($request["REAL_FILE_PATH"]){ // real page
            $url = $request["REAL_FILE_PATH"];
        } else {
            $url = $request->getScriptName(); 
        } 
        if (!preg_match($reg, $url)){ 
            return false; // no page in mask
        } else {
            return true; // page in mask
        }
	}
    /**
     * add js script in head
     * @param type $arSettings
     */
    protected static function setRecaptchaScript($arSettings){
        $recaptchaOptions = array(
            'size' => $arSettings['size'],
            'theme' => $arSettings['theme'],
            'badge' => $arSettings['badge'],
            'version' => $arSettings['version'],
            'action' => $arSettings['action'],
            'lang' => LANGUAGE_ID,
            'key' => $arSettings['key'],
        );
        $script = "<script type='text/javascript'>";
        $script .= "window['recaptchaFreeOptions']=" . \CUtil::PhpToJsObject($recaptchaOptions) . ";";
        $script .= "</script>";
        Asset::getInstance()->addString($script);
        $minJs = (Option::get("main","use_minified_assets", "Y") == "Y") ? ".min" : "";
        $scriptPath =  Application::getDocumentRoot() . '/bitrix/js/twim.recaptchafree/script'.$minJs.'.js';
        $scriptCode = file_get_contents($scriptPath);
        $scriptDef = '<script type="text/javascript">' . $scriptCode . '</script>';
        Asset::getInstance()->addString($scriptDef);
    }
    /**
     * check recaptcha response
     * @return boolean
     */
	private static function checkReCaptcha($reCaptchaCode, $arSettings)
	{
        try {
            $context = Application::getInstance()->getContext(); 
            $server = $context->getServer();
            $google_url="https://www.google.com/recaptcha/api/siteverify";

            $data = array(
                'secret' => $arSettings["secretkey"],
                'response' => $reCaptchaCode,
                'remoteip' => $server->get('REMOTE_ADDR')
            );

            $httpClient = new HttpClient();
            $response = $httpClient->post($google_url, $data);

            if (empty($response)) {
                throw new SystemException('Wrong argument type, "json" expected');
            }

            $res = json_decode($response, true);
            
            if($res['success']){ // success unswer from google
                return true;
            } else {
                if ($response['error-codes']) {
                    throw new SystemException($response['error-codes']);
                } else {
                    throw new SystemException('Wrong argument type, "array" expected');
                }
            }
        } catch (\Exception $exception){
              \CEventLog::Add(array(
                "SEVERITY" => "WARNING",
                "AUDIT_TYPE_ID" => "TWIM.RECAPTCHAFREE_ERROR",
                "MODULE_ID" => self::MODULE,
                "ITEM_ID" => self::MODULE,
                "DESCRIPTION" => Loc::getMessage('GCT_ERROR') . ' ' . $exception->getMessage() 
            ));
        } 
		return false;
	}
}