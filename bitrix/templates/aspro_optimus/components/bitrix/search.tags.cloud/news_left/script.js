$(document).ready(function(){
	$(".news__tegs a").click(function(){
		var obj= $(this);
		$.ajax({
		  url: obj.attr('href'),
		  success: function(data) {				
				var d = $('<div>'+data+'</div>');
				var newTitle = $('title').text();
				$(".float_banners").remove();
				$(".new__title").remove();				
				$(".news__tegs li").removeClass('active');				
				obj.parent().addClass('active');

				if ($(".filter_block",d)) $(".filter_block").html($(".filter_block",d).html());
				$(".articles-list").html($(".articles-list",d).html());				 
				$(".bottom_nav").html($(".bottom_nav",d).html());
				$(".sort_filter").html($(".sort_filter",d).html());				
				
				history.pushState('', newTitle, obj.attr('href'));
				
				$('html, body').animate({scrollTop:$('.right_block').offset().top}, 'slow');																
		  }
		});
		return false;
	});
							
});