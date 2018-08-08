var _ = require('underscore');
var Backbone = require('backbone');
var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');
var Viewport = require('../mixins/viewport');

var EventsMap = _.extend({

	queue : {},

	firstPage: true,

	mapDocument: function(){
		var self = this;

		$(function(){
			self.trigger('documentReady');
		});

		this.$window = $(window);
		this.$body = $('body');

		this.windowHeight = this.$window.height();
		this.windowWidth = this.$window.width();

		$(window).one('load',function(){

			self.trigger('windowLoad');

		}).resize(_.debounce(function(){self.onResizeWindow()}, 300, false)).scroll(function(){




		});

		//window.addEventListener('scroll', this.throttle(function(){self.onScroll()}, 100));

		document.addEventListener("DOMContentLoaded", function(event) {
    			self.trigger('DOMContentLoaded');
  		});


  		this.enqueue(
  			['documentReady','windowLoad','DOMContentLoaded'],
  			function(){
  				self.trigger('pageReady');
  			}, 'DOM');

		this.on("fetchProjectsJSON menuLoad pageLoad", this.enqueueCounter, {"event":"firstLoad", "counter":3});


		$('.scroll-top').click(function(){

			TweenMax.to(window, 0.8, {scrollTo:0, ease:Power3.easeOut});

		});

	},

	throttle: function(fn, wait) {
	  var time = Date.now();
	  return function() {
	    if ((time + wait - Date.now()) < 0) {
	      fn();
	      time = Date.now();
	    }
	  }
	},

	onScroll: function(){
		if(this.$window.scrollTop() > this.windowHeight / 2){

				this.$body.addClass('show-on-scroll');
			} else {
				this.$body.removeClass('show-on-scroll');
			}
	},

	onResizeWindow: function(){

		var self = this;
		self.windowHeight = $(window).height();
		self.windowWidth = $(window).width();
		self.trigger('windowIsResized');

	},

	enqueueCounter: function(){

		var event = this['event'];
		var origin = this['origin'];
		var counter = this['counter'];

		if(typeof origin == 'undefined'){
			origin = 'global'
		}

		if(typeof EventsMap.queue[origin] == 'undefined'){

			EventsMap.queue[origin] = {};

		}

		if(typeof EventsMap.queue[origin][event] == 'undefined'){

			EventsMap.queue[origin][event] = {
				'counter': counter,
				'origin': origin,
				'name': event
			}

		}

		if(EventsMap.queue[origin][event]['counter'] == 0 || EventsMap.queue[origin][event]['origin'] != origin) {

			EventsMap.queue[origin][event]['counter'] = counter;
			EventsMap.queue[origin][event]['origin'] = origin

		} else {

			EventsMap.queue[origin][event]['counter'] -= 1;

		}

		if(EventsMap.queue[origin][event]['counter'] == 0){

			EventsMap.trigger(EventsMap.queue[origin][event]['name']);

		}

	},

	killAll: function(){

		delete EventsMap.queue;

	},

	enqueue : function(events, properties, id, enqueue_object){

		var self = this;

		if (typeof id == 'object'){

			enqueue_object = id;

		} else if (typeof enqueue_object == 'undefined'){

			enqueue_object = this;

		}

		if(typeof properties == 'undefined'){
			properties = {};
		}


		if(typeof id != 'string'){
			id = 'global'
		}

		if(typeof enqueue_object.enqueueStatus == 'undefined'){

			enqueue_object.enqueueStatus = {};

		}

		enqueue_object.enqueueStatus[id] = 0;

	    if(typeof events.length != 'undefined'){
	        var regular_increment = 100/events.length + 1;
	    } else {
	        var regular_increment = false
	    }

	    for(event in events){

	      if(!regular_increment){
	        var increment = events[event];
	        var event_name = event;
	      } else {
	        var increment = regular_increment;
	        var event_name = events[event];
	      }


	      if(event_name.substring(0, 5) == "this."){
	      	var event_object = enqueue_object;
	      	event_name = event_name.replace("this.", "");
	      } else {
	      	var event_object = self;
	      }



	      event_object.once(event_name,EventsMap.enqueueStep,{id:id,object:enqueue_object,increment:increment,properties:properties});

	    }

  },

  enqueueStep : function(){


  	if(this.object.killed) return;

  	this.object.enqueueStatus[this.id] += this.increment;

  	if(typeof this.properties == 'function'){
  			var callback = this.properties;
  			this.properties = {};
			this.properties.callback = callback;
	};


  	if(typeof this.properties.args  == 'undefined'){
   		this.properties.args = null
	};

    if(typeof this.properties.loaderFunction  == 'function'){

      this.properties.loaderFunction(this.object.enqueueStatus[this.id], this.properties.args);

    };

    if(typeof this.properties.callback == 'function' && this.object.enqueueStatus[this.id] >= 100){

    		this.properties.callback.call(this.object,this.properties.args);
	};

  },

  safe_trigger : function(event, ref_object){

  		var ref_object_passed = true;

  		if (typeof ref_object != 'object'){

  			ref_object = this;
  			ref_object_passed = false;

  		}

  		if(ref_object.killed) return;

  		ref_object.trigger(event);

  		if(ref_object_passed){

  			this.trigger(event);

  		}

  }

}, Backbone.Events);


EventsMap.mapDocument();

module.exports = EventsMap;
