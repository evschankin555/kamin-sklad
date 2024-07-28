$(document).ready(function(){
	
	
	
	$('.detail-art_picture_block>img').each(function(key,value){
		
		var href = $(value).attr('src');
		
		$(value).wrap('<a class="fancy" href="'+href+'"></a>');
		
		
	});
	
});