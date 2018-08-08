'use strict';
var Viewport = require('../mixins/viewport');
var Scroll = require('../mixins/scroll_smooth');
var ScrollController = require('../mixins/scroll_controller');
var StickyHeader = require('../mixins/scroll_header');
var Parallax = require('../mixins/scroll_parallax');
var ScrollLoad = require('../mixins/scroll_lazyload');
var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');

var exports = {};

/********************************/
/****  Add Viewport elements ****/
/********************************/

Viewport.addElement(['#globalNav','#localNav']);


/*****************/
/****  scroll ****/
/*****************/


var scroll = new Scroll({
  native: true,
  preload: true,
	ease: 0.1
});

exports.scroll = scroll;

/*********************/
/** sections scroll **/
/*********************/

exports.controllers = {};
var mainController = null;

$('.vs-controller').each(function(i){

      var $this = $(this);

			var active = ($this.hasClass('vs-controller-active')) ?  true : false;

			var scrollController = new ScrollController(scroll, {
					el:this,
					active: active,
					isMain: active
			});

      if(active) mainController = scrollController;

      $this.find('.parallax-wrapper').each(function(){
            new Parallax(this, scrollController);
      });


      $(window).on('load', function(){

        $this.find('*[data-lazyload], *[data-lazyload-trigger]').each(function(){
              new ScrollLoad(this, scrollController);
        });

      });


      exports.controllers[i] = scrollController;
});


$('.vs-trigger').click(function(e){
		e.preventDefault();
		var scrollController = $($(this).data('controller')).data('scroll-controller');
		scrollController.show();
});


/*********************/
/**** nav scroll *****/
/*********************/

Viewport['#localNav'].isFixed = false;

scroll.addListener('realScroll', StickyHeader);

module.exports = exports;
