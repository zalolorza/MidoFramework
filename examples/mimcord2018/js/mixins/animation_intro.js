'use strict';

var $ = require('jquery');
var _ = require('underscore');
var Backbone = require('backbone');
var Scroll = require('../mixins/scroll_controllers');
var ScrollController = Scroll.controllers[0];
var ScrollVideo = require('../mixins/scroll_video');

var AnimationIntro = function(){

    this.init = function(){

      _.extend(this, Backbone.Events);

      if(!ANIMATION_INTRO) return;

      this.page_template = PAGE_TEMPLATE;

      this.on({
        "start": this.onStart,
        "end": this.onEnd,
        "load": this.onLoad
      }, this);

      var self = this;
      $(window).on('load',function(){self.start();})
    }

    this.start = function(){

      var self = this;

      switch(this.page_template){
        case 'home':

          this.totalMedia = $(".home-bike-img").length;

          $(".home-bike-img").one("load", function() {
              self.totalMedia--;
              if(self.totalMedia == 0) self.animateHome();
          }).each(function() {
              if(this.complete) {
                self.totalMedia--;
                if(self.totalMedia == 0) {
                  self.animateHome();
                }
              }
          });

          break;

        case 'product':

            this.video = new ScrollVideo({
                el: document.getElementById('video-intro'),
                debug: false,
                invert : true,
                duration: 0.6
            });

            this.video.on('startingPoint',function(){

              self.trigger("end");

            }).on('load',function(){

              self.trigger("load");

            }).load();

          break;
        default:

          self.trigger("load");
          break;
      }
    }

    this.animateHome = function(){

          var self = this;
          TweenMax.to('.hidden-before-intro.grad-gray-top',1.0,{opacity:0.26,delay:0.3});
          TweenMax.fromTo('.bike-wrapper-1',0.8,{opacity:0,y:'10%'},{opacity:1,y:'0%',delay:0});
          TweenMax.fromTo('.bike-wrapper-2',0.8,{opacity:0,y:'24.57%'},{opacity:1,y:'16.07%',delay:0.08});
          TweenMax.fromTo('.bike-wrapper-3',0.8,{opacity:0,y:'39.14%'},{opacity:1,y:'32.14%',delay:0.16});
          TweenMax.fromTo('.bike-wrapper-4',0.8,{opacity:0,y:'24.57%'},{opacity:1,y:'16.07%',delay:0.24});
          TweenMax.fromTo('.bike-wrapper-5',0.8,{opacity:0,y:'10%'},{opacity:1,y:'0%',delay:0.32});

          TweenMax.staggerTo('.hidden-text-before-intro',1.6,{opacity:1,y:0,delay:0.6},0.06);

          setTimeout(function(){
              self.trigger("load");
          },1120);
    }
    this.onLoad = function(){
        var self = this;
        switch(this.page_template){
          case 'home':
            this.trigger('start');
            break;
          case 'product':
            TweenMax.to('.hidden-before-intro.grad-gray-top',0.8,{opacity:0.26, onComplete: function(){
              TweenMax.staggerTo('.hidden-text-before-intro',1.3,{opacity:1,y:0,delay:0.3,onComplete:function(){
                TweenMax.to('.hidden-btn-before-intro',0.8,{opacity:1});
              }},0.1);
              this.trigger('start');
          }, onCompleteScope : this});
            break;
        }
        $('.header-nav').removeClass('hidden');


    }


    this.onStart = function(){
      switch(this.page_template){
        case 'home':

          break;
        case 'product':
            var self = this;
            this.video.set('transitionTime',2500);
            this.video.on('startingPoint', function(){
                self.video.set('transitionTime',1200);
            });
            TweenMax.to(this.video.el,1,{opacity:1});
            this.video.init();
          break;
        default:

          break;
      }

    }

    this.onEnd = function(){

    }

    this.init();
}


module.exports = new AnimationIntro();
