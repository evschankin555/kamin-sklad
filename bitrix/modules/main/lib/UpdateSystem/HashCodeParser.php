<? namespace Bitrix\Main\UpdateSystem;$GLOBALS['____759978982']= array(base64_decode('YmFzZTY0X'.'2'.'RlY'.'2'.'9'.'kZQ=='),base64_decode('dW5zZX'.'JpYWxpe'.'mU='),base64_decode('b3Blb'.'n'.'Nzb'.'F92ZXJpZnk'.'='),base64_decode('dW5zZXJpY'.'Wxp'.'emU='));if(!function_exists(__NAMESPACE__.'\\___340258990')){function ___340258990($_908342508){static $_2004352937= false; if($_2004352937 == false) $_2004352937=array('YWxsb'.'3dlZF9jbGFzc2Vz','aW5mbw='.'=',''.'c2lnbmF0dXJl',''.'c2hhM'.'jU2V2l0'.'aFJTQU'.'VuY'.'3J5cHRp'.'b2'.'4'.'=','aW5mbw='.'=','YWx'.'sb3dlZ'.'F9jbGFzc2Vz','RXJy'.'b3IgdmVyaWZ5IG'.'9wZW5zc2wgW0'.'h'.'DUF'.'A'.'wMV0=','LS0tLS1'.'CRU'.'dJTiBQVUJMSUMgS'.'0'.'VZLS'.'0tLS0KTUlJQk'.'l'.'qQU5CZ2txaG'.'tpRzl3M'.'EJBUUV'.'G'.'QUF'.'PQ0FROEF'.'NSUlCQ2dLQ0'.'FRRUE2'.'aGN4SX'.'F'.'paXRVWlJN'.'d1lpdW'.'t'.'TVQpoO'.'X'.'hhNWZFRF'.'lsY2'.'NiVzN'.'2ajhB'.'d'.'mE'.'zNXZL'.'cVZONGlCO'.'XRxQ1g3a'.'l'.'U'.'4NnFBYTJ2MzdtYlRGNn'.'B'.'jWTZIR1BB'.'aFJGCmJw'.'bndYT1k3W'.'Ud4QjFuU0tadkUr'.'a'.'kF'.'SYmlMTE'.'Jn'.'WjFjR'.'zZa'.'MGR'.'1'.'d'.'TVpMVh'.'ocE'.'l'.'S'.'T'.'DFj'.'TjB'.'Ia'.'D'.'VmZXpwal'.'hDNk8K'.'WXh'.'ZcTBuVG9I'.'VGp5UmIxeWN6d'.'3'.'RtaVJ3WXF'.'1ZFh'.'nL'.'3hXeHBwcXd'.'GMHRV'.'bG'.'QzUUJyM2k2'.'OE'.'I4anFNbSt'.'UamRl'.'QQp1L2Z'.'nMUowSk'.'d0UjQve'.'ks0Rz'.'dZSk52aG1'.'1aHJ'.'SR2t5QV'.'FWMFR'.'Wd'.'TV'.'MRXVnU3'.'hqQ'.'XB'.'SbUlKUU5'.'IUU'.'1L'.'ME'.'VoOTN'.'3Ck1ab0ZvUHA5U2dKN0dhR'.'lU4a3'.'pT'.'K0'.'VR'.'Y25'.'0WXh'.'i'.'MU5I'.'VUpVSXZUZGl1UlVlRkts'.'eV'.'Rk'.'eEl'.'ySD'.'ZDT'.'C'.'8'.'vYX'.'BN'.'S'.'DMK'.'R'.'ndJR'.'E'.'F'.'RQU'.'IKLS0tLS'.'1FTkQ'.'g'.'UFVCTEl'.'DIEtFWS0tLS0t');return base64_decode($_2004352937[$_908342508]);}}; use Bitrix\Main\Application; use Bitrix\Main\Security\Cipher; use Bitrix\Main\Security\SecurityException; class HashCodeParser{ private string $_1641447268; public function __construct(string $_1641447268){ $this->_1641447268= $_1641447268;}  public function parse(){ $_691712226= $GLOBALS['____759978982'][0]($this->_1641447268); $_691712226= $GLOBALS['____759978982'][1]($_691712226,[___340258990(0) => false]); if($GLOBALS['____759978982'][2]($_691712226[___340258990(1)], $_691712226[___340258990(2)], $this->__608075339(), ___340258990(3)) == round(0+0.33333333333333+0.33333333333333+0.33333333333333)){ $_870050521= Application::getInstance()->getLicense()->getHashLicenseKey(); $_1527590224= new Cipher(); $_1050475670= $_1527590224->decrypt($_691712226[___340258990(4)], $_870050521); return $GLOBALS['____759978982'][3]($_1050475670,[___340258990(5) => false]);} throw new SecurityException(___340258990(6));} private function __608075339(): string{ return ___340258990(7);}}?>