
import $ from 'jquery';
import _ from 'lodash';

var supportPageOffset = window.pageXOffset !== undefined;
var isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");

$(window).on('scroll', _.throttle(function(){
  var y = supportPageOffset ? window.pageYOffset : isCSS1Compat ? document.documentElement.scrollTop : document.body.scrollTop;
	$(window).trigger( "scrollthrottle", [y] ); 
},100));