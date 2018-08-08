'use strict';

var $ = require('jquery');
var _ = require('underscore');

function headerOverlap(){
   var $reference = $('.header-overlap-reference');
   var $header = $('header#header.format-overlap');
   var overlap = parseInt($reference.innerHeight()/2);
   $header.css('margin-bottom',-overlap+'px');
}

$(function(){
    headerOverlap();
});
$(window).resize(function(){
    _.throttle(headerOverlap(), 300); 
}).on('load',function(){
    headerOverlap(); 
})



