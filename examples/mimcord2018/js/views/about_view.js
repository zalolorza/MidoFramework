'use strict';

var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');
require('../mixins/viewport');
var slick = require('slick-carousel');
require('magnific-popup');

global.ShowOnScroll = function(elements, offset){

    this.init = function(elements, offset){
        this.selector = elements;
        this.$elements  = $(elements);
        this.offset = offset;
        this.isFree = true;
        this.extraHeight = 0;
        this.resize();
        this.attachEvents();
    }


    this.attachEvents = function(){
        var self = this;
        $(window).on('scrollthrottled',function(e){ 
             self.work();
        }).on('resizethrottled',function(e){ 
            self.resize();
       });
    }


    this.resize = function(){

        this.windowHeight = parseInt($(window).height()*(1.0-this.offset));
        this.work();
    }

    this.onComplete = function($element){
        $element.addClass('in-view');
        this.isFree = true;
        this.work();
    }

    this.animateMilestone = function($element, delay){
        
        if(typeof delay == 'undefined') delay = 0;

        var $texts = $element.find('.milestone-animate-text');
        var $image = $element.find('.milestone-thumnail');
        var x = 15;
        if($image.hasClass('milestone-thumnail-right')){
            x = -15;
        }
        TweenMax.staggerFromTo($texts.slice(0,2),0.8,{opacity:0,y:10},{delay:delay, y:0,opacity:1},0.2);
        TweenMax.fromTo($image,1.4,{scale:0.95,opacity:0,y:15,x:x},{delay:delay+0.2,scale:1,y:0,x:0,opacity:1,ease:Elastic.easeOut.config(1.0, 0.5)});
        TweenMax.to($element.find('.separator-last'),0.9,{height:'100%',ease:Power0.easeNone});
    }

    this.work = function(){
        var self = this;
        if(!this.isFree) return;
        if(this.$elements.length == 0) {
            this.isFree = false;
            return;
        };
        var $element = this.$elements.first();
        if((window.scrollY + this.windowHeight) > $element.offset().top){
            this.isFree = false;
            var time = ($element.prev().innerHeight()-this.extraHeight)*1.7/1000;
            if(typeof time == 'undefined') time = 0;
            var $separator = $element.prev().find('.separator');
            var tl = new TimelineMax({onComplete:function(){
                self.onComplete($element);
            }});
            tl.to($separator,time,{height:'100%',ease:Power0.easeNone, onComplete:function(){
                    self.extraHeight = 0;
                    $element.find('.milestone-text-extra').each(function(){
                        var $extraMilestone = $(this);
                        self.extraHeight = $extraMilestone.position().top + parseInt($extraMilestone.css('margin-top'));
                        var timeExtra = self.extraHeight*2.7/1000;
                        var tlExtra = new TimelineMax();
                        tlExtra.to($element.find('.separator'),timeExtra,{height:self.extraHeight,ease:Power0.easeNone, onComplete:function(){
                            self.animateMilestone($extraMilestone);
                        }})
                            .fromTo($extraMilestone.find('.before'),0.8,{scale:0.6,opacity:0},{scale:1, opacity:1, ease:Elastic.easeOut.config(2.5, 0.4)});
                    });
                    self.animateMilestone($element,0.1);
                }})
                .fromTo($element.find('.before').first(),0.8,{scale:0.6,opacity:0},{scale:1, opacity:1, ease:Elastic.easeOut.config(2.5, 0.4)});
                
           
           
            this.$elements = self.$elements.not($element);

            
            
           
        }
    }

  
    this.init(elements, offset);

}

$(window).on('load',function(){
    new ShowOnScroll('.show-on-scroll',0.2);
});

$(function(){
    $('#about-us-gallery').on('init', function(){
        $('#about-us-gallery .image-link').magnificPopup({
          type: 'image',
          mainClass: 'mfp-with-zoom', 
          zoom: {
            enabled: true,
            duration: 300,
            easing: 'ease-in-out',
            opener: function(openerElement) {
              return openerElement.is('img') ? openerElement : openerElement.find('img');
            }
          },
          gallery:{
            enabled:true
          },
          image: {
            titleSrc: 'title'
          }
        });
      });
    $('#about-us-gallery').slick({
        infinite: true,
        centerMode: true,
        centerPadding: '20%',
        slidesToShow: 2,
        slidesToScroll: 1,
        dots: false,
        initialSlide: 0,
        responsive: [
            { 
                breakpoint: 1500,
                settings: {
                    slidesToShow: 1,
                    centerPadding: '31.74%',
                }
              },
              { 
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    centerPadding: '20%',
                }
              },
            { 
                breakpoint: 550,
                settings: {
                    slidesToShow: 1,
                    centerPadding: '10.39%',
                }
              }
        ]
      });
    
});