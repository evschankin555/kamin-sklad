<script>
<?=$LABEL?>setups.addPage('props',{
	trClick: function(wat,mode){
		if(wat.hasClass('chosenTr') && typeof mode === 'undefined')
			wat.removeClass('chosenTr');
		else
			wat.addClass('chosenTr');
			
		<?=$LABEL?>setups.getPage('props').checkTr();
	},
	
	checkTr : function(){
		var strToWrite='';
		$('[id^="IPOLMO_payer_"]').each(function(){
			var selfId=$(this).attr('id').substr(13);
			strToWrite+=selfId+'{';
			$('#payer_'+selfId).find('.chosenTr').each(function(){
				strToWrite+=$(this).children('.codeIsHere').html()+',';
			});
			strToWrite+="}|";
		});
		$('[name="IPOLMO_OPT_PROPS"]').val(strToWrite);
		$('#test').val(strToWrite);
	},
	
	payerClick : function(wat){
		$('#payer_'+wat).find('.propsTable').each(function(){<?=$LABEL?>setups.getPage('props').trClick($(this),1)});
	},
	
	groupClick : function(wat,where){
		$('#payer_'+where+' #group_'+wat).find('.propsTable').each(function(){<?=$LABEL?>setups.getPage('props').trClick($(this),1)});
	}
});

<?=$LABEL?>setups.addPage('events',{
	addRow : function(){
		$('[name="IPOLMO_OPT_ADDEVENTS[]"]:last').closest('tr').after("<tr><td colspan='2' style='text-align:center;'><input type='text' name='IPOLMO_OPT_ADDEVENTS[]' value='' size='50'></td></tr>");
	}
});
</script>
<style>
	#moduleTable td {padding: 5px 10px; border-bottom: 1px solid #aaa;}
	#moduleTable {border-collapse: collapse; max-width: 1000px;}
	#moduleTable thead td {background-color: #E2E6D4; color: #555; }
	#moduleTable tbody tr:hover {background-color: #FEFEFE !important; }
	#moduleTable tbody td {background-color: transparent; !important }


	.propsPayer td{
		font-size: 14px;
		font-weight: 700;
		text-align: center;
		padding: 5px;
		color: #9F5959;
		cursor: pointer;
	}
	.propsGroup td{
		border-bottom: 1px dashed black;
		text-align: center;
		padding: 5px;
		color: #9F5959;
		cursor: pointer;
	}
	.propsTable td{
		border: 1px dashed black;
		text-align: center;
		padding: 5px;
		color: #9F5959;
		cursor: pointer;
	}			
	.propsTable:hover,.propsPayer:hover,.propsGroup:hover{background-color: #D4D4E3;}
	.chosenTr{background-color: #D1FAD1;}
	.IPOLMO_detailTable{
		margin:auto !important;
		border-collapse:collapse;
	}
	.IPOLMO_detailTable td,.IPOLMO_detailTable th{
		padding: 2px 8px;
	}
</style>
<?
ShowParamsHTMLByArray($arAllOptions["main"]);
?>