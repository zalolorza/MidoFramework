'use strict';

var $ = require('jquery');
var EventsMap = require('../mixins/events_map');
var TweenMax = require('gsap/TweenMax');



var Styles = {

	init: function(){

		this.setStyleTags();
		this.listen();
		this.windowResize();

	},

	setStyleTags: function(){

		$('body').append('<style id="windowResize"></style>');

	},

	listen: function(){

		EventsMap.on({

			'documentReady': this.windowResize ,
			'appendPage': this.newPage,
			'windowIsResized': this.windowResize

		});

		EventsMap.on('documentReady',function(){
				$('body').removeClass('avoid-transitions');
		})

	},

	newPage: function(){

		ImageResize.resizeAll();

	},

	windowResize: function(lastIteration){

		var headerHeight = $('#header').height();

		var windowHeight = EventsMap.windowHeight;
		var windowWidth = EventsMap.windowWidth;

		var style = '';

		style += ' .full-page {height: '+windowHeight+'px}';

		$('#windowResize').html(style);


		EventsMap.trigger('dynamicCSS');

	}


};

Styles.init();
