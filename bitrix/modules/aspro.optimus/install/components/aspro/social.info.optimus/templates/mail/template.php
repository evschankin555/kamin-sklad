<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="mail_soc_wrapper" style="text-align:center;padding-top:20px;font-size:0px;">
	<?if( !empty( $arParams["VK"] ) && $arParams["VK"] != "\"" ){?>
		<a href="<?=$arParams["VK"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/vk.png" alt="<?=GetMessage("VKONTAKTE")?>" title="<?=GetMessage("VKONTAKTE")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["ODN"] ) && $arParams["ODN"] != "\""  ){?>
		<a href="<?=$arParams["ODN"]?>" target="_blank"  class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/odn.png" alt="<?=GetMessage("ODN")?>" title="<?=GetMessage("ODN")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["FACE"] ) && $arParams["FACE"] != "\""  ){?>
		<a href="<?=$arParams["FACE"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/facebook.png" alt="<?=GetMessage("FACEBOOK")?>" title="<?=GetMessage("FACEBOOK")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["TWIT"] ) && $arParams["TWIT"] != "\""  ){?>
		<a href="<?=$arParams["TWIT"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/twitter.png" alt="<?=GetMessage("TWITTER")?>" title="<?=GetMessage("TWITTER")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["INST"] ) && $arParams["INST"] != "\""  ){?>
		<a href="<?=$arParams["INST"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/inst.png" alt="<?=GetMessage("INST")?>" title="<?=GetMessage("INST")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["MAIL"] ) && $arParams["MAIL"] != "\""  ){?>
		<a href="<?=$arParams["MAIL"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/mail.png" alt="<?=GetMessage("MAIL")?>" title="<?=GetMessage("MAIL")?>" />
		</a>
	<?}?>
	<?if( !empty( $arParams["GOOGLEPLUS"] ) && $arParams["GOOGLEPLUS"] != "\""  ){?>
		<a href="<?=$arParams["GOOGLEPLUS"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/gplus.png" alt="<?=GetMessage("GOOGLEPLUS")?>" title="<?=GetMessage("GOOGLEPLUS")?>" /> 
		</a>
	<?}?>
	<?if( !empty( $arParams["YOUTUBE"] ) && $arParams["YOUTUBE"] != "\""  ){?>
		<a href="<?=$arParams["YOUTUBE"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.optimus/images/youtube.png" alt="<?=GetMessage("YOUTUBE")?>" title="<?=GetMessage("YOUTUBE")?>" /> 
		</a>
	<?}?>
</div>