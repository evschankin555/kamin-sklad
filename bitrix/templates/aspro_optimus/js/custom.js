/*
You can use this file with your scripts.
It will not be overwritten when you upgrade solution.
*/
var catTextClosed = true;

function setGetParameter(paramName, paramValue, host)
{
  var url = window.location.pathname;
  var hash = location.hash;
  url = url.replace(hash, '');
  if (url.indexOf("?") >= 0)
  {
    var params = url.substring(url.indexOf("?") + 1).split("&");
    var paramFound = false;
    params.forEach(function(param, index) {
      var p = param.split("=");
      if (p[0] == paramName) {
        params[index] = paramName + "=" + paramValue;
        paramFound = true;
      } 
    });
    if (!paramFound) params.push(paramName + "=" + paramValue);
    url = url.substring(0, url.indexOf("?")+1) + params.join("&");
  }
  else
    url += "?" + paramName + "=" + paramValue;
  window.location.href = 'http://'+host+url + hash;
  return false;
}

$(document).ready(function(){
			
	$(".fb-img-content").fancybox({  // картики  (для группы картинок использовать одинаковый rel="group_name")
		openEffect	: 'elastic',
		closeEffect	: 'elastic',
		titleShow : true,
		helpers : {
			thumbs : {
				width  : 150,
				height : 150
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
			
	$('.changeCity').click(function()
	{
		var name = "cities";
		var hash = "#cities";		
		$('body').find('.'+name+'_frame').remove();
		$('body').append('<div class="'+name+'_frame popup"></div>');		
		$('.'+name+'_frame').jqm({trigger: '.'+name+'_frame.popup',onHide: function(hash) { onHidejqm(name,hash); }, onLoad: function( hash ){ onLoadjqm( name , hash , "", false); }, ajax: arOptimusOptions["SITE_DIR"]+'ajax/locations.php'});
		$('.'+name+'_frame.popup').click();
		return false;				
	});
     
	$('#cat-text-open').click(function () {
		if(catTextClosed){
		  $(this).html('Свернуть');
		  
		  $('#cat-text-top').velocity({ height: "100%" }, { duration: 500 });
		  catTextClosed = false;
		}else{
		  $(this).html('Подробнее');
		  $('#cat-text-top').velocity({ height: "118px" }, "easeInSine");
		  catTextClosed = true;
		}
	});
	  		
});
