'use strict';

var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');
var scrollTo = require('gsap/ScrollToPlugin');

var Menu = function(){

    this.isOpen = false;

    this.attachEvents = function(){
        var self = this;
        var menuSelector = '#menuMobile';
        this.$menu = $(menuSelector);
        this.$menuDesktop = $('#nav');
        $('.openMenu').click(function(e){
            e.preventDefault;
            self.open();
        });

        this.isSticky = false;

        $(window).on('isResized scrollthrottle', function(){
            var scrollPosition = 30;
            if(self.isSticky && $(window).scrollTop() < scrollPosition){
                self.$menuDesktop.removeClass('sticky');
                self.isSticky = false;
            } else if(!self.isSticky && $(window).scrollTop() > scrollPosition) {
                self.$menuDesktop.addClass('sticky');
                self.isSticky = true;
            }
        });

        $(window).on('isResized', function(){
            if(self.isOpen && window.innerWidth > 992){
                self.close();
            }
        });

        $('.closeMenu, '+menuSelector).click(function(e){
            e.preventDefault;
            self.close();
        });
        $('.switchMenu').click(function(e){
            e.preventDefault;
            self.switch();
        });
    }

    this.open = function(){

        if(this.isOpen) return;
        
        this.$menu.show();
        this.$menu.find('.background').fadeIn();

        //TweenLite.to(window, 0.3, {scrollTo:0, ease:Power4.easeOut});
        
        //$('html').height($(window).height).css('overflow-y','hidden');

        $('#menuTrigger .mask').fadeIn();
       

        TweenMax.staggerFromTo('.mainMenu a',0.7,{y:20,opacity:0},{y:0, opacity:1,ease:Back.easeOut.config(3),onCompleteScope:this,onComplete:function(){
            this.isOpen = true;
        }},-0.05);
        TweenMax.to('.line-1, .line-3',0.3,{y:0,ease: Power3.easeInOut,onCompleteScope:this,onComplete:function(){
            $('.line-2').hide();
            $('html').addClass('no-scroll');
            //$('body').addClass('menu-is-open');
            TweenMax.to('.line-1',0.65,{rotation:-45,ease: Elastic.easeOut.config(1.2, 0.5)});
            TweenMax.to('.line-3',0.85,{rotation:45,ease: Elastic.easeOut.config(1.2, 0.5)});
        }});
    
        
    }

    this.close = function(){

        if(!this.isOpen) return;
       
        var self = this;
    

        TweenMax.staggerTo('.mainMenu a',0.5,{y:-50, opacity:0, ease:Back.easeIn.config(2), onComplete:function(){
            self.$menu.find('.background').fadeOut(function(){
                self.$menu.hide();
                self.isOpen = false;
            });
        }},0.03);

        TweenMax.to('.line-1, .line-3',0.3,{rotation:0,ease: Back.easeIn.config(2),onCompleteScope:this,onComplete:function(){
            $('.line-2').show();
            //$('html').height('auto').css('overflow-y','scroll');
            $('html').removeClass('no-scroll');
            //$('body').removeClass('menu-is-open');
            $('#menuTrigger .mask').fadeOut();
            TweenMax.to('.line-1',0.65,{y:-12,ease: Elastic.easeOut.config(1.2, 0.5)});
            TweenMax.to('.line-3',0.85,{y:12,ease: Elastic.easeOut.config(1.2, 0.5),onComplete:function(){
                
            }});
        }});
        
    }

    this.switch = function(){
            if(this.isOpen) {
                this.close();
            } else {
                this.open();
            }
    }

    this.attachEvents();
}

module.exports = new Menu();