'use strict';

var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');
var scrollTo = require('gsap/ScrollToPlugin');

$(function(){
    $('.scroll-top').click(function(){
        TweenLite.to(window, 0.7, {scrollTo:0, ease:Power3.easeOut});
    });

    $('*[data-scroll-to]').click(function(){
        TweenLite.to(window, 1.0, {scrollTo:$(this).data('scroll-to'), ease:Power4.easeOut});
    });
});



