'use strict';

var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');

require('../mixins/scroll_throttled');

var Parallax = function(elements){

    this.velocity = 0.3;

    this.init = function(elements){
        this.selector = elements;
        this.$elements  = $(elements);
        this.setElements();
        this.attachScroll();
    }

    this.attachScroll = function(){
        var self = this;
        $(window).on('scrollthrottled',function(e){ 
             self.parallax();
        });
    }

    this.setElements = function(){
        var self = this;

        this.$elements.each(function( index, value ) {
            var $this = $(this);
            if($this.data('velocity')){
                this.velocity = $this.data('velocity');
            } else {
                this.velocity = self.velocity;
            }
            if($this.hasClass('parallax-opacity')){
                this.parallaxOpacity = true;
            } else {
                this.parallaxOpacity = false;
            }
        });

        this.resizeElements();
    }

 

    this.resizeElements = function(){
  
        var self = this;
        
        this.windowHeight = $(window).innerHeight();
        

        this.$elements.each(function() {
            var $this = $(this);
            var $parent = $this.parent();
            this.parentTop = $parent.offset().top;
            this.parentBottom = this.parentTop+$parent.innerHeight();

            if(this.parentTop > 0 ){

                var parallaxDistance = (self.windowHeight + $parent.innerHeight()) * this.velocity;
                var elementExtra = parseInt($this.innerHeight() - $parent.innerHeight());
        

                if(elementExtra < parallaxDistance) {

                    this.parallaxFactor = elementExtra/parallaxDistance;

                } else {
                    this.parallaxFactor = 1;
                }

                if(this.parentTop > this.windowHeight) {
                    this.offsetScroll = this.parentTop - this.windowHeight;
                } else {
                    this.offsetScroll = 0;
                }
                
            } else {
                this.offsetScroll = 0;
                this.parallaxFactor = 1;
            }
        });

        this.parallax();
    }

    this.parallax = function(){
        var self = this;
        this.scrollTop = window.scrollY;
        this.scrollBottom = this.scrollTop + this.windowHeight;

        this.$elements.each(function() {
            self.parallaxElement(this);
        });
    }

    this.parallaxElement = function(element){
        
        if(element.parentTop > this.scrollBottom || element.parentBottom < this.scrollTop) return;

        var parallaxAmount = (this.scrollTop-element.offsetScroll)*element.parallaxFactor*element.velocity;

        
            
        if(element.parallaxOpacity){
                var opacity = 1-(0.003*this.scrollTop);
        } else {
                var opacity = 1;
        }


        TweenMax.set(element,{y:parallaxAmount, opacity:opacity});
    }

    this.init(elements);

}

module.exports = new Parallax('.parallax')