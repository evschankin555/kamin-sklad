/**
*   ReCaptcha 2.0 Google js
*	Shevtcoff S.V. 
*	date 25.08.19
*	time 12:10
*/
;
"use strict";

(function(window, document){
    
    var Recaptchafree = Recaptchafree || {};
    Recaptchafree.form_submit;
    var settigs = window['recaptchaFreeOptions'];
    Recaptchafree.loadApi = function(){
        var detectRecaptchaApi = document.getElementById("recaptchaApi");
        if(!detectRecaptchaApi){
            var script = document.createElement('script');
            script.async = true;
            script.id = "recaptchaApi";
            script.src = "//www.google.com/recaptcha/api.js?onload=RecaptchafreeLoaded&render=explicit&hl=" + settigs["lang"];
            script.onerror = function() {
              console.error("Failed to load \"www.google.com/recaptcha/api.js\"");
            };
            document.getElementsByTagName("head")[0].appendChild(script); 
        } 
        return true;
    };

    Recaptchafree.loaded = function(){
        Recaptchafree.renderById = Recaptchafree.renderByIdAfterLoad;
        Recaptchafree.renderAll();
        /* If invisible recaptcha on the page */
        if(settigs["size"] === "invisible") {
            document.addEventListener('submit',function(e){
                if(e.target && e.target.tagName === "FORM"){
                    var g_recaptcha = e.target.querySelector('div.g-recaptcha');
                    var widget_id = g_recaptcha.getAttribute("data-widget");
                    grecaptcha.execute(widget_id);
                    Recaptchafree.form_submit = e.target;
                    e.preventDefault();    
                } 
            }, false);
            /* hide grecaptcha-badge, if multi invisible recaptcha */
            var badges = document.querySelectorAll('.grecaptcha-badge'); 
            for (var i = 1; i < badges.length; i++) {
                badges[i].style.display="none";
            }
        }
        /* update after ajax jQuery */
        if (window.jQuery){
            jQuery(document).ajaxComplete(function() {
                Recaptchafree.reset();
            });
        }
        /* update after ajax BX  */
        if (typeof window.BX.addCustomEvent !== "undefined"){
            window.BX.addCustomEvent('onAjaxSuccess', function () {
                Recaptchafree.reset();   
            });
        }
    };

    Recaptchafree.renderAll = function() { 
        var elements = document.querySelectorAll('div.g-recaptcha');
        for (var i = 0; i < elements.length; i++) {
            if(!elements[i].hasAttribute("data-widget")) {
                Recaptchafree.renderById(elements[i].getAttribute("id"));
            } 
        }
    };

    Recaptchafree.renderByIdAfterLoad = function(id) { 
        var el = document.getElementById(id);
        var widget = grecaptcha.render(el, {
            'sitekey'   : el.hasAttribute("data-sitekey") ? el.getAttribute("data-sitekey") : settigs["key"],
            'theme'     : el.hasAttribute("data-theme") ? el.getAttribute("data-theme") : settigs["theme"],
            'size'      : el.hasAttribute("data-size") ? el.getAttribute("data-size") : settigs["size"],
            'callback'  : el.hasAttribute("data-callback") ? el.getAttribute("data-callback") : settigs["callback"],
            'badge'     : el.hasAttribute("data-badge") ? el.getAttribute("data-badge") : settigs["badge"]
        });
        el.setAttribute("data-widget", widget);
    };

    Recaptchafree.reset = function() { 
        Recaptchafree.renderAll(); 
        var elements = document.querySelectorAll('div.g-recaptcha[data-widget]');
        for (var i = 0; i < elements.length; i++) {
            var id = elements[i].getAttribute('data-widget');
            if(window.grecaptcha){
                grecaptcha.reset(id);
            }
        }
    };

    Recaptchafree.submitForm = function(token){
        if(Recaptchafree.form_submit !== undefined){
            var x = document.createElement("INPUT"); /* create token input  */
            x.setAttribute("type", "hidden");  
            x.name = "g-recaptcha-response";
            x.value = token;
            Recaptchafree.form_submit.appendChild(x);  /* append current form  */
            var elements = Recaptchafree.form_submit.elements;
            for (var i = 0; i < elements.length; i++) {
                if(elements[i].getAttribute("type") === "submit")  {
                    var submit_hidden = document.createElement("INPUT"); /* create submit input hidden  */
                    submit_hidden.setAttribute("type", "hidden");  
                    submit_hidden.name = elements[i].name;
                    submit_hidden.value = elements[i].value;
                    Recaptchafree.form_submit.appendChild(submit_hidden);  /* append current form  */
                }
            }
            document.createElement('form').submit.call(Recaptchafree.form_submit); /* submit form  */
        }       
    };

    Recaptchafree.throttle = function(func, wait, options) {
        var context, args, result;
        var timeout = null;
        var previous = 0;
        if (!options) options = {};
        var later = function() {
          previous = options.leading === false ? 0 : new Date().getTime();
          timeout = null;
          result = func.apply(context, args);
          if (!timeout) context = args = null;
        };
        return function() {
          var now = new Date().getTime();
          if (!previous && options.leading === false) previous = now;
          var remaining = wait - (now - previous);
          context = this;
          args = arguments;
          if (remaining <= 0 || remaining > wait) {
            if (timeout) {
              clearTimeout(timeout);
              timeout = null;
            }
            previous = now;
            result = func.apply(context, args);
            if (!timeout) context = args = null;
          } else if (!timeout && options.trailing !== false) {
            timeout = setTimeout(later, remaining);
          }
          return result;
        };
    };

    Recaptchafree.replaceCaptchaBx = function(){
        var images = document.body.querySelectorAll('form img');
        var src = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
        Recaptchafree.renderAll();
        for (var i = 0; i < images.length; i++) {
            var img = images[i];
            if (/\/bitrix\/tools\/captcha\.php\?(captcha_code|captcha_sid)=[0-9a-z]+/i.test(img.src)) {
                img['src'] = src;
                img.removeAttribute("width");
                img.removeAttribute('height');
                img.style.display = 'none';
                if(!img.parentNode.querySelector(".g-recaptcha")){
                    var recaptchaId = 'd_recaptcha_' +Math.floor(Math.random()*16777215).toString(16);
                    var replacement = document.createElement('div');
                    replacement['id'] = recaptchaId;
                    replacement['className'] = 'g-recaptcha';
                    img.parentNode.appendChild(replacement);
                    Recaptchafree.renderById(recaptchaId);
                }    
            }
        }
        var inputs = document.querySelectorAll('form input[name="captcha_word"]');
        for (var i = 0; i < inputs.length; i++) {
            var inp = inputs[i];
            if (inp.style.display !== "none") {
                inp.style.display = "none";
            } 
            inp.value = "";
        }  
    };

    Recaptchafree.init = function(){
        Recaptchafree.renderById = Recaptchafree.loadApi;
        document.addEventListener("DOMContentLoaded", function () {
            Recaptchafree.renderAll();
            Recaptchafree.replaceCaptchaBx();
            var throttled = Recaptchafree.throttle(Recaptchafree.replaceCaptchaBx, 200);
            var MutationObserver = window.MutationObserver
                || window.WebKitMutationObserver
                || window.MozMutationObserver;

            if (!!MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                       if(mutation.type === 'childList' && mutation.addedNodes.length > 0 && mutation.addedNodes[0].tagName !== "IFRAME"){
                        throttled();  
                       }
                    });
                });
                observer.observe(document.body, {
                    attributes: false,
                    characterData: false,
                    childList: true,
                    subtree: true,
                    attributeOldValue: false,
                    characterDataOldValue: false
                });
            } else {
                document.addEventListener('DOMNodeInserted', function(mutation) {
                    throttled();
                });
            }
        });
    };
    
    Recaptchafree.init(); 
    window.Recaptchafree = Recaptchafree;
    window.RecaptchafreeLoaded = Recaptchafree.loaded;
    window.RecaptchafreeSubmitForm = Recaptchafree.submitForm;

})(window, document);