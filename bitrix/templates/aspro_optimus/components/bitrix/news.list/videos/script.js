$(document).ready(function(){
	
if($('.video_popup').length){
	
	$(".video_popup").fancybox({
		'transitionIn'	: 'none',
		'transitionOut'	: 'none',
		'type' : 'iframe'	
	});
}
	
});




$(".fb-img").fancybox({  // картики  (для группы картинок использовать одинаковый rel="group_name")
	openEffect	: 'elastic',
	closeEffect	: 'elastic',
	helpers : {
		thumbs : {
			width  : 80,
			height : 60
		},
		overlay : {
			locked : false
		}
	},
	tpl:{
		closeBtn : '<a title="'+BX.message('FANCY_CLOSE')+'" class="fancybox-item fancybox-close" href="javascript:;"></a>',
		next     : '<a title="'+BX.message('FANCY_NEXT')+'" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
		prev     : '<a title="'+BX.message('FANCY_PREV')+'" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
	},	
});

BX.addCustomEvent('onAjaxSuccess', function() {
	$(".fb-img").fancybox({  // картики  (для группы картинок использовать одинаковый rel="group_name")
		openEffect	: 'elastic',
		closeEffect	: 'elastic',
		helpers : {
			thumbs : {
				width  : 80,
				height : 60
			},
			overlay : {
				locked : false
			}
		},
		tpl:{
			closeBtn : '<a title="'+BX.message('FANCY_CLOSE')+'" class="fancybox-item fancybox-close" href="javascript:;"></a>',
			next     : '<a title="'+BX.message('FANCY_NEXT')+'" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
			prev     : '<a title="'+BX.message('FANCY_PREV')+'" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
		},	
	});
});