'use strict';

var $ = require('jquery');
var slick = require('slick-carousel');
var $slider = $('#slick-home');
var TweenMax = require('gsap/TweenMax');
var transitionSlides = 680;
var maxTranslation = 16.667;
var ease = Cubic.easeInOut;
//var ease = null;
var virtualCurrentSlide = 1;
var isAnimating = false;

$slider.slick({
    dots: false,
    infinite: false,
    speed: transitionSlides,
    slidesToShow: 1,
    cssEase: 'ease-in-out',
    autoplay: true,
    autoplaySpeed: 3200
  });

var slickParallax = function(el, slick, currentSlide, nextSlide){

     if (currentSlide > nextSlide) {
        var currentPosition = "-"+(maxTranslation*2)+"%";
     } else {
        var currentPosition = "0%";
     }

     var transitionTime = transitionSlides/1000;
     TweenMax.to($(slick.$slides[currentSlide]).find('.image-background'),transitionTime,{x:currentPosition, ease: ease});
     TweenMax.to($(slick.$slides[nextSlide]).find('.image-background'),transitionTime,{x: "-"+maxTranslation+"%", ease: ease});

};

$slider.on('beforeChange', function(event, slick, currentSlide, nextSlide){
    isAnimating = true;
    if(currentSlide != nextSlide){
       virtualCurrentSlide = currentSlide;
    }
    
    slickParallax(this, slick, virtualCurrentSlide, nextSlide);
  });

$slider.on('afterChange', function(event, slick, currentSlide){
    isAnimating = false;
});

var pageXinitial = 0;
var windowWith = $(window).width();
var currentSlide = 0;
var prevSlide = false;
var nextSlide = 1;
var slickObject =  $slider.slick('getSlick');
var totalSlides = slickObject.slideCount;
var hasDragged = false;


function onDrag(e){
    if(isAnimating) return;
    
    hasDragged = true;
    var percentage = maxTranslation/windowWith*(pageXinitial-e.pageX);
   
    var moveCurrent = false;
    if(percentage > 0 && $nextSlideImg){

       virtualCurrentSlide = nextSlide;
       moveCurrent = true;
       var x = -(maxTranslation*2)+percentage;
       TweenMax.set($nextSlideImg,{x: x+"%"});
    } else if (percentage < 0 && $prevSlideImg) {

       virtualCurrentSlide = prevSlide; 
       var x = percentage;
       moveCurrent = true;
       TweenMax.set($prevSlideImg,{x: x+"%"});
    }

    if(moveCurrent){
       var x = -maxTranslation+percentage;
       TweenMax.set($currentSlideImg,{x: x+"%"});
    }
}
var $currentSlideImg = false;
var $prevSlideImg = false;
var $nextSlideImg = false;

$slider.on('mousedown touchstart', function(e){

    hasDragged = false;

    if(e.originalEvent.type == 'touchstart'){
        e = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
    }

    pageXinitial = e.pageX;

    
    windowWith = $(window).width();
    currentSlide = $slider.slick('slickCurrentSlide');
    $currentSlideImg = $(slickObject.$slides[currentSlide]).find('.image-background');
    prevSlide = currentSlide -1;
    if(currentSlide > 0){
        $prevSlideImg = $(slickObject.$slides[prevSlide]).find('.image-background'); 
    } else {
        $prevSlideImg = false;
    }
   
    nextSlide = currentSlide +1;
    if(nextSlide < totalSlides){
        $nextSlideImg = $(slickObject.$slides[nextSlide]).find('.image-background');
    } else {
        $nextSlideImg = false;
    }
    

    $(this).one('mouseup', function(e){
   
        if(!hasDragged){
            $slider.slick('slickNext');
        }
     $(this).off('mousemove');

    }).on('mousemove', function(e){
   
      onDrag(e);
     
     
    });
   
}).bind('touchmove',function(e){
    
    var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
    
    onDrag(touch);
});
$(function(){
    $slider.addClass('slick-ready');
    $('.slick-arrow').on('click mouseup mousedown',function(e){
        e.stopPropagation();
    });
});



