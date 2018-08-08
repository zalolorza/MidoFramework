'use strict';

var $ = require('jquery');
var _ = require('underscore');

function resizeSingleNews(){
   var $img = $('#single-news .news-thumbnail');
   if($img.length <= 0) return;
   var $content = $('#single-news .news-content');
   var minHeight = parseInt($img.height()-parseInt($('#single-news').css('padding-top')));
   $content.css('min-height',minHeight+'px');
}

$(function(){
    resizeSingleNews();
});
$(window).resize(function(){
    _.throttle(resizeSingleNews(), 300); 
}).on('load',function(){
    resizeSingleNews(); 
})



