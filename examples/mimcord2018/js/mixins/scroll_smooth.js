'use strict';
var Smooth = require('smooth-scrolling');
Smooth = Smooth.default;
var Viewport = require('../mixins/viewport');
var $ = require('jquery');


/* Constructor */

function Scroll(arg) {
	var arg = (typeof arg !== 'undefined') ?  arg : {};
	if(typeof arg.section == 'undefined' || !arg.section){
			$('body').prepend('<div class="vs-section"> </div>');
	};

  Smooth.call(this, arg); // call super constructor.

	this.vars.$section = $('.vs-section');
	this.vars.$body = $('body');

	this.listeners = {'scroll': [], 'realScroll':[]};

	this.init();
}

Scroll.prototype = Object.create(Smooth.prototype);
Scroll.prototype.constructor = Scroll;


/* Extend Run */

Scroll.prototype.run = function(){

        if (this.isRAFCanceled) return

        this.vars.current += (this.vars.target - this.vars.current) * this.vars.ease
        this.vars.current < .1 && (this.vars.current = 0)

        this.rAF = requestAnimationFrame(this.run)

        this.vars.last = this.vars.current;

				this.callListeners('scroll',this.vars.current.toFixed(4));
				this.callListeners('realScroll',window.scrollY);

}

Scroll.prototype.deactivate = function(){

	this.cancelAnimationFrame();

}


Scroll.prototype.reactivate = function(){

	if (this.isRAFCanceled) {
			this.isRAFCanceled = false
	}


	this.requestAnimationFrame()
}


/* Setters & getters */

Scroll.prototype.get = function(){

		return this.vars.current;

}

Scroll.prototype.getReal = function(){

		return window.scrollY;

}

Scroll.prototype.getReal = function(){

		return window.scrollY;

}

Scroll.prototype.set = function(val){


		this.vars.last = val;
		this.vars.current = val;
		$(window).scrollTop(val);
		this.callListeners('scroll',val.toFixed(4));

		return this;

}

Scroll.prototype.setHeight = function(val){


		this.vars.$body.css({height:val});
		this.vars.$section.css({height:val});
		this.resize();
}


/* listeners */

Scroll.prototype.addListener = function(ev, callback, scope){

		var callback = (typeof callback !== 'undefined') ?  callback : this;
		this.listeners[ev].push({scope: scope, callback:callback});

}

Scroll.prototype.callListeners = function(ev, arg){
	var listeners = this.listeners[ev];
	for (var i=0; i<listeners.length; i++) {
		listeners[i].callback.call(listeners[i].scope, arg);
	}
}


module.exports = Scroll;
