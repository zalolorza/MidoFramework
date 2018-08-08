'use strict';

var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');

var Menu = function(){

    this.isOpen = false;

    this.attachEvents = function(){
        var self = this;
        $('.openMenu').click(function(e){
            e.preventDefault;
            self.open();
        });
        $('.closeMenu, #menu').click(function(e){
            e.preventDefault;
            self.close();
        });
        $('.switchMenu').click(function(e){
            e.preventDefault;
            self.swicth();
        });
    }

    this.open = function(){
        $('#menu').show();
        this.isOpen = true;
        TweenMax.staggerFromTo('.background-menu',0.8,{opacity:0,x:'-20%'},{opacity:1,x:'0%',ease: Power2.easeOut},0.12);
        TweenMax.fromTo('.logo-menu',1.2,{opacity:0,x:'-60%'},{opacity:1,x:'-50%',ease: Power2.easeOut, delay:0.25});
        TweenMax.staggerFromTo('.tagline-menu',1,{opacity:0,x:'-10%'},{opacity:1,x:'0%',ease: Power2.easeOut, delay:0.5},0.12);
        TweenMax.staggerFromTo('.li-menu',1,{opacity:0,x:'-15%'},{opacity:1,x:'0%',ease: Power2.easeOut, delay:0.25},0.12);

        var easeCross = CustomEase.create("custom", "M0,0 C0,0 0.158,0.18 0.216,0.458 0.267,0.706 0.277,1.133 0.398,1.134 0.486,1.134 0.507,0.914 0.586,0.914 0.644,0.914 0.69,1 0.78,1.016 0.87,1.032 1,1 1,1");

		TweenMax.fromTo('#menu .close-menu .line1',0.25,{opacity:1, top:'0%',x:'0%',y:'-50%',width:0},{delay:0.2, width:32, ease:easeCross});
        TweenMax.fromTo('#menu .close-menu .line2',0.35,{opacity:1, top:'100%',x:'0%',y:'-50%',width:0},{delay:0.45,width:32, ease:easeCross});
        
        $('#menuTrigger').fadeOut();
        
    }

    this.close = function(){
        this.isOpen = false;
        var self = this;
        TweenMax.staggerTo('.background-menu',0.8,{opacity:0,x:'20%',ease:Power1.easeInOut, delay:0.18},0.12,function(){
            if(self.isOpen) return;
            $('#menu').hide();
        });
        TweenMax.to('.logo-menu',1,{opacity:0,x:'40%',ease:  Back.easeIn.config(1), delay:0.0});
        TweenMax.staggerTo('.li-menu',1.1,{opacity:0,x:'50%', ease: Back.easeInOut.config(0.6), delay:0.02},0.1);
        TweenMax.staggerTo('.tagline-menu',1,{opacity:0,x:'100%', ease: Power2.easeInOut, delay:0.15},0.12);
        
        TweenMax.to('#menu .close-menu .line1',0.3,{delay:0.15, width:0, opacity:0, ease:Back.easeIn.config(1.7)});
        TweenMax.to('#menu .close-menu .line2',0.3,{delay:0,width:0, opacity:0, ease:Back.easeIn.config(1.7)});
        
        $('#menuTrigger').delay(300).fadeIn();
    }

    this.switch = function(){

    }

    this.attachEvents();
}

module.exports = new Menu();

