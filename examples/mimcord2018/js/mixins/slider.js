'use strict';

var $ = require('jquery');
var EventsMap = require('../mixins/events_map');
require('slick-carousel');
var _ = require('underscore');
//require('../mixins/images_loader');

var Slider = function(el,prop) {

	this.prop = {

			arrows: true,
			fade: true,
	  	cssEase: 'ease',
  		autoplay: true,
  		autoplaySpeed: 3000,
  		speed:800,
  		prevArrow:'<div class="slider-arrow-prev slick-arrow-prev slider-arrow hide-touch"></div>',
  		nextArrow:'<div class="slider-arrow-next slick-arrow-next slider-arrow hide-touch"></div>',
  		waitForAnimate:true,
  		dots: true,
  		dotsClass: 'slick-numbers list-unstyled list-inline'

	};

	this.callback = function(){};

	this.init = function(el,prop){

		if(typeof prop == 'function'){
			this.callback = prop;
		} else {
			for (var k in prop) {
							if (prop.hasOwnProperty(k)) {
								 this.prop[k] = prop[k];
							}
					}

		}


		var self = this;

		this.el = el;
		this.$el = $(el);

		if(this.$el.hasClass('slick-boxed')){

				this.$wrapper = this.$el.parent();
				this.$wrapper.addClass('slick-boxed-wrapper');
				this.boxed = true;


		} else {
				this.boxed = false;
		}

		this.prop = _.extend(this.prop,prop);

		if(BROWSER.is_touch){
			this.prop.fade = false;
			this.prop.arrows = false;
		}

		this.addListeners().loadImg(function(){
				self.build();
				self.$el.addClass('window-loaded');
				self.callback();
		});

	};

	this.addListeners = function(){

		var self = this;

		EventsMap.on({
			'windowIsResized': this.resize,
			'windowResize': this.resize,
			'windowHeightModified': this.resize
		},this);



		return this;

	};

	this.loadImg = function(callback){


				this.$el.find('[data-lazy-image]').loadImages(function(){ callback(); });

	};

	this.build = function(){

		this.$el.slick(this.prop);

		this.$el.data('slider',true);

		if(!BROWSER.is_touch){
			this.$el.click(function(){
				$(this).slick('next');
			});
		}

		this.$img = this.$el.find('.slick-slide-img');
		this.$arrows = this.$el.find('.slick-arrow');
		this.$numbers = this.$el.find('.slick-numbers');
		this.$arrowLeft = this.$el.find('.slick-arrow-prev');
		this.$arrowRight = this.$el.find('.slick-arrow-next');

		if(this.boxed){
				this.$numbers.addClass('slick-numbers-boxed');
				this.$arrows.addClass('slick-arrow-boxed');
		}
		this.resize();

	},

	this.resize = function(){
		if(this.boxed){
				var slidesHeight = this.$wrapper.innerHeight();
		} else {
				var slidesHeight = this.$el.innerHeight();
		}
		this.$slides = this.$el.find('.slick-slide');
		this.$slides.height(slidesHeight);
		this.$arrows.css({top:this.$img.innerHeight()/2,opacity:1});
		if(this.boxed){
				var containerLeft = $('.container-fluid > .row > div').offset().left;
				this.$arrowLeft.css({left:containerLeft});
				this.$arrowRight.css({right:containerLeft});
		}
	}

	this.init(el,prop);

}

$.fn.extend({

     slider: function(props){

     		if (typeof props == 'undefined') {

     			this.props = {};

     		} else {

     			this.props = props;
     		}


         this.index = 0;

         var el = this;

         this.each(function(){


            if(typeof $(this).data('slider') == 'undefined'){

               	  var slider = new Slider(this,el.props);

                    el.index++;

               };

          });
     }

});


module.exports = Slider;
