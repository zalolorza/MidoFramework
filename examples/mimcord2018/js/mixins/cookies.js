'use strict';

var $ = require('jquery');
var EventsMap = require('../mixins/events_map');

var Cookies = function(){

	this.visible = true;

	this.init = function(){

		this.$message = $('#cookiesMessage');
	
		this.$close = this.$message.find('#cookiesMessage-close');

		if(this.checkCookies()){

			
			this.remove();

		} else {

			this.listen();

		}

	};

	this.checkCookies = function(){

		return this.readCookie('acceptCookies');

	};

	this.acceptCookies = function(){

		this.createCookie('acceptCookies',true,false);

	};

	this.createCookie = function(name,value,days) {
	    var expires = "";
	    if (days) {
	        var date = new Date();
	        date.setTime(date.getTime() + (days*24*60*60*1000));
	        expires = "; expires=" + date.toUTCString();
	    }
	    document.cookie = name + "=" + value + expires + "; path=/";
	};

	this.readCookie = function(name) {
	    var nameEQ = name + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0;i < ca.length;i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1,c.length);
	        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	    }
	    return false;
	};

	this.eraseCookie = function(name) {
	    this.createCookie(name,"",-1);
	};

	

	this.listen = function(){

		var self = this;

		var initEvent = 'windowLoad';

		EventsMap.once(initEvent, function(){

			setTimeout(function(){

				self.show();

			},700);

		});

		$('body a').one('click',function(){

			self.acceptCookies();

			self.hide(Power3.easeIn);

		});

		this.$message.click(function(){

			self.acceptCookies();

			self.hide(Elastic.easeIn.config(1, 0.7));

		});


		EventsMap.on('cookiesMessageShow',function(){

			setTimeout(function(){

				/*if(self.visible && Scroll.scrollTop > 300){

					self.hide(Back.easeIn.config(2));

				}*/

			},60000);

		});

	};

	this.show = function(){
		
		var easeCross = CustomEase.create("custom", "M0,0 C0,0 0.158,0.18 0.216,0.458 0.267,0.706 0.277,1.133 0.398,1.134 0.486,1.134 0.507,0.914 0.586,0.914 0.644,0.914 0.69,1 0.78,1.016 0.87,1.032 1,1 1,1");

		TweenMax.to(this.$message.find('#cookiesMessage-content'),1.0,{delay:0.1,opacity:1});
		TweenMax.to(this.$message,0.75,{y:'0%',ease: Elastic.easeOut.config(0.8, 0.7), onComplete:function(){
			EventsMap.trigger('cookiesMessageShow');
		}});

		TweenMax.fromTo(this.$close.find('.line1'),0.35,{opacity:1, top:'0%',x:'0%',y:'-50%',width:0},{delay:0.4, width:28, ease:easeCross});
		TweenMax.fromTo(this.$close.find('.line2'),0.35,{opacity:1, top:'100%',x:'0%',y:'-50%',width:0},{delay:0.65,width:28, ease:easeCross});

	};

	this.hide = function(ease){

		this.visible = false;

		TweenMax.to(this.$message,0.75,{y:'100%',ease: ease,
		            onCompleteScope:this,
		            onComplete:this.remove});

	};

	this.remove = function(){

		this.visible = false;
		this.$message.remove();

	};

	this.init();
};

var cookies = new Cookies();

module.exports = cookies;
