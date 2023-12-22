<?###TWIM.RECAPTCHAFREE###
if (!defined('WIZARD_DEFAULT_TONLY') && Bitrix\Main\Loader::includeModule('twim.recaptchafree'))
{
	$message = 'Not passed verification reCaptcha';

	$MESS['/bitrix/modules/main/lang/ru/classes/general/user.php']["main_user_captcha_error"] = $message;
	$MESS['/bitrix/modules/form/lang/ru/include.php']["FORM_WRONG_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/iblock.element.add.form/lang/ru/component.php']["IBLOCK_FORM_WRONG_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/blog.post.comment/lang/ru/component.php']["B_B_PC_CAPTCHA_ERROR"] = $message;
	$MESS['/bitrix/components/bitrix/catalog.product.subscribe/lang/ru/ajax.php']["CPSA_INCCORECT_INPUT_CAPTHA"] = $message;
	$MESS['/bitrix/components/bitrix/forum.comments/lang/ru/class.php']["POSTM_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/forum.message.send/lang/ru/component.php']["F_BAD_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/forum.topic.reviews/lang/ru/action.php']["POSTM_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/idea.comment.list/lang/ru/component.php']["B_B_PC_CAPTCHA_ERROR"] = $message;
	$MESS['/bitrix/components/bitrix/infoportal.element.add.form/lang/ru/component.php']["IBLOCK_FORM_WRONG_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/main.feedback/lang/ru/component.php']["MF_CAPTCHA_WRONG"] = $message;
	$MESS['/bitrix/components/bitrix/main.register/lang/ru/component.php']["REGISTER_WRONG_CAPTCHA"] = $message;
	$MESS['/bitrix/components/bitrix/sale.notice.product/lang/ru/component.php']["NOTIFY_NO_CAPTHA"] = $message;
	$MESS['/bitrix/components/bitrix/socialnetwork.blog.post.comment/lang/ru/component.php']["B_B_PC_CAPTCHA_ERROR"] = $message;
	$MESS['/bitrix/modules/main/lang/ru/classes/general/user.php']['MAIN_FUNCTION_REGISTER_CAPTCHA'] = $message;
	$MESS['/bitrix/components/bitrix/main.feedback/lang/ru/component.php']['MF_CAPTHCA_EMPTY'] = $message;
	$MESS['/bitrix/modules/vote/lang/ru/classes/general/vote.php']['VOTE_BAD_CAPTCHA'] = $message;
	$MESS['/bitrix/components/bitrix/blog.post.comment.list/lang/ru/class.php']['B_B_PC_CAPTCHA_ERROR'] = $message;
	$MESS['/bitrix/components/bitrix/blog.post.comment/lang/ru/class.php']['B_B_PC_CAPTCHA_ERROR'] = $message;
}
###///TWIM.RECAPTCHAFREE###?>