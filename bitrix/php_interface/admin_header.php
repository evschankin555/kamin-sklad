<?php
CJSCore::Init(array("jquery"));
if (strpos($_SERVER["REQUEST_URI"], "/bitrix/admin/") !== false)
{
?>
	<script type="text/javascript">
		$(function () {
				$('#tr_BASE_PRICE td').last().append(' &nbsp; <a class="updprice" href="#">обновить у регионов</a>');
				$('.updprice').click(function(){
					var p = $('#CAT_BASE_PRICE').val();
					$('#prices_simple .internal input[type=text]').each(function(index, element) {
                        $(this).val(p);
						$(this).change();
                    });
					return false;
				});
		});
	</script>
<?
}
