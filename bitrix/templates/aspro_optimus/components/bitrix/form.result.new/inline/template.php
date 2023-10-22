<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$frame = $this->createFrame()->begin('')?>
<?
$bLeftAndRight = false;
if(is_array($arResult["QUESTIONS"])){
	foreach($arResult["QUESTIONS"] as $arQuestion){
		if(strpos($arQuestion["STRUCTURE"][0]["FIELD_PARAM"],'left') !== false){
			$bLeftAndRight = true;
			break;
		}
	}
}

?>
<div class="form inline <?=$arResult["arForm"]["SID"]?>">
	<!--noindex-->
<?if($arResult["arForm"]["SID"] != "PODBOR"):?>
	<div class="form_head">
		<?if($arResult["isFormTitle"] == "Y"):?>
			<h4><?=$arResult["FORM_TITLE"]?></h4>
		<?endif;?>
		<?if($arResult["isFormDescription"] == "Y"):?>
			<div class="form_desc"><?=$arResult["FORM_DESCRIPTION"]?></div>
		<?endif;?>
	</div>
<?endif;?>
	<?if($arResult["isFormErrors"] == "Y" || strlen($arResult["FORM_NOTE"])):?>
		<div class="form_result <?=($arResult["isFormErrors"] == "Y" ? 'error' : 'success')?>">
			<?if($arResult["isFormErrors"] == "Y"):?>
				<?=$arResult["FORM_ERRORS_TEXT"]?>
			<?else:?>
				<script type="text/javascript">
				$(document).ready(function(){
					if(arOptimusOptions['COUNTERS']['USE_FORMS_GOALS'] !== 'NONE'){
						var eventdata = {goal: 'goal_webform_success' + (arOptimusOptions['COUNTERS']['USE_FORMS_GOALS'] === 'COMMON' ? '' : '_<?=$arParams['WEB_FORM_ID']?>'), params: <?=CUtil::PhpToJSObject($arParams, false)?>, result: <?=CUtil::PhpToJSObject($arResult, false)?>};
						BX.onCustomEvent('onCounterGoals', [eventdata]);
					}
				});
				</script>
				<?$successNoteFile = SITE_DIR."include/form/success_{$arResult["arForm"]["SID"]}.php";?>
				<?if(file_exists($_SERVER["DOCUMENT_ROOT"].$successNoteFile)):?>
				<?$APPLICATION->IncludeFile($successNoteFile, array(), array("MODE" => "html", "NAME" => "Form success note"));?>
				<?else:?>
					<?=GetMessage("FORM_SUCCESS");?>
				<?endif;?>
			<?endif;?>
		</div>
	<?endif;?>
	<?=$arResult["FORM_HEADER"]?>
	<?=bitrix_sessid_post();?>
	<div class="form_body">
		<?if(is_array($arResult["QUESTIONS"])):?>
			<?if(!$bLeftAndRight):?>
				<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
					<?COptimus::drawFormField($FIELD_SID, $arQuestion);?>
				<?endforeach;?>
			<?else:?>
				<div class="form_left">
					<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
						<?if(strpos($arQuestion["STRUCTURE"][0]["FIELD_PARAM"],'left') !== false):?>
<? if ($arQuestion["STRUCTURE"]["0"]["FIELD_TYPE"]=="checkbox") { ?><div class="filter filter_hide_star"><? } ?>
							<?COptimus::drawFormField($FIELD_SID, $arQuestion);?>
<? if ($arQuestion["STRUCTURE"]["0"]["FIELD_TYPE"]=="checkbox") { ?></div><? } ?>
						<?endif;?>
					<?endforeach;?>
				</div>
				<div class="form_right">
					<?foreach($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):?>
						<?if(strpos($arQuestion["STRUCTURE"][0]["FIELD_PARAM"],'left') === false):?>
<? if ($arQuestion["STRUCTURE"]["0"]["FIELD_TYPE"]=="checkbox") { ?><div class="filter filter_hide_star"><? } ?>                        
							<?COptimus::drawFormField($FIELD_SID, $arQuestion);?>
<? if ($arQuestion["STRUCTURE"]["0"]["FIELD_TYPE"]=="checkbox") { ?></div><? } ?>                            
						<?endif;?>
					<?endforeach;?>
				</div>
			<?endif;?>
		<?endif;?>
		<div class="clearboth"></div>
    
<?$APPLICATION->IncludeComponent("bitrix:main.userconsent.request", ".default", Array(
	"ID" => "1",	// Соглашение
		"IS_CHECKED" => "N",	// Галка согласия проставлена по умолчанию
		"AUTO_SAVE" => "Y",	// Сохранять автоматически факт согласия
		"IS_LOADED" => "Y",	// Загружать текст соглашения сразу
		"INPUT_NAME"=> "agree",
		"REPLACE" => array(
			"button_caption" => "Отправить",
			"fields" => array(
				0 => "Email",
				1 => "Телефон",
				2 => "Имя",
			),
		)
	),
	false
);?>
    
    
    
		<?if($arResult["isUseCaptcha"] == "Y"):?>
			<div class="form-control captcha-row clearfix">
				<label><span><?=GetMessage("FORM_CAPRCHE_TITLE")?>&nbsp;<span class="star">*</span></span></label>
				<div class="captcha_image">
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"])?>" border="0" />
					<input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"])?>" />
					<div class="captcha_reload"></div>
				</div>
				<div class="captcha_input">
					<input type="text" class="inputtext captcha" name="captcha_word" size="30" maxlength="50" value="" required />
				</div>
			</div>
		<?endif;?>
		<div class="clearboth"></div>
	</div>
	<div class="form_footer">
		<?/*<button type="submit" class="button medium" value="submit" name="web_form_submit" ><span><?=$arResult["arForm"]["BUTTON"]?></span></button>*/?>
		<input type="submit" class="button medium" value="<?=$arResult["arForm"]["BUTTON"]?>" name="web_form_submit">
		<button type="reset" class="button medium transparent" value="reset" name="web_form_reset" ><span><?=GetMessage('FORM_RESET')?></span></button>
		<script type="text/javascript">
		$(document).ready(function(){
			$('form[name="<?=$arResult["arForm"]["VARNAME"]?>"]').validate({
				highlight: function( element ){
					$(element).parent().addClass('error');
				},
				unhighlight: function( element ){
					$(element).parent().removeClass('error');
				},
				submitHandler: function( form ){
					var agree = false;
					if($('form input[name="agree"]').attr('checked'))
					{
						console.log('AGREE');
						agree = true;
					}
					else
					{
						console.log('NOT AGREE'); 
					}
					if( $('form[name="<?=$arResult["arForm"]["VARNAME"]?>"]').valid() && agree){
						form.submit();

						setTimeout(function() {
							$(form).find('button[type="submit"]').attr("disabled", "disabled");
						}, 300);
					}
				},
				errorPlacement: function( error, element ){
					error.insertBefore(element);
				}
			});

			if(arOptimusOptions['THEME']['PHONE_MASK'].length){
				var base_mask = arOptimusOptions['THEME']['PHONE_MASK'].replace( /(\d)/g, '_' );
				$('form[name=<?=$arResult["arForm"]["VARNAME"]?>] input.phone, form[name=<?=$arResult["arForm"]["VARNAME"]?>] input[data-sid=PHONE]').inputmask('mask', {'mask': arOptimusOptions['THEME']['PHONE_MASK'] });
				$('form[name=<?=$arResult["arForm"]["VARNAME"]?>] input.phone, form[name=<?=$arResult["arForm"]["VARNAME"]?>] input[data-sid=PHONE]').blur(function(){
					if( $(this).val() == base_mask || $(this).val() == '' ){
						if( $(this).hasClass('required') ){
							$(this).parent().find('label.error').html(BX.message('JS_REQUIRED'));
						}
					}
				});
			}
		});
		</script>
	</div>
	<?=$arResult["FORM_FOOTER"]?>
	<?
	/*$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
        "AREA_FILE_SHOW" => "file", 
        "AREA_FILE_SUFFIX" => "inc", 
        "AREA_FILE_RECURSIVE" => "Y", 
	"PATH" => "/include/personal.php"
    )
	);*/
	?>
	<!-- <div>Нажимая на кнопку "отправить" или "зарегистрироваться" я даю своё согласие на <a href="/company/politic/">обработку персональных данных</a> и на <a href="/company/public-rules/">правила публикации отзывов</a></div> -->
	<!--/noindex-->
</div>
<?$frame->end()?>