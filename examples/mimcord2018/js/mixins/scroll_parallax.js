'use strict';
var $ = require('jquery');
var TweenMax = require('gsap/TweenMax');
var Viewport = require('../mixins/viewport');

function Parallax(el, ScrollController) {

      /**** constructor ****/

      this.init = function(){

        $(window).on('load',function(){
        //  window.dispatchEvent(new Event('resize'));
        });

        this.el = el;
        this.$el = $(el);
        this.controller = ScrollController;

        var self = this;
        var $layers = this.$el.find('[data-parallax]');
        var $layersOpacity = this.$el.find('[data-opacity]');

        this.scenes = [];
        this.scenesOpacity = [];
        var data = $layers.first().data('parallax');


        this.duration = 0;

        $layers.each(function(){

            var data = $(this).data('parallax');
            self.scenes.push({
                  el: this,
                  duration : data.duration,
                  offset : {
                      'start': data.offset[0],
                      'end': data.offset[1],
                      'distance': data.offset[1]-data.offset[0],
                      'original': {
                        'start': data.offset[0],
                        'end': data.offset[1],
                        'distance': data.offset[1]-data.offset[0]
                      }
                  }
            });

            if(data.duration > self.duration) self.duration = data.duration;

        });

        $layersOpacity.each(function(){
            var data = $(this).data('opacity');
            self.scenesOpacity.push({
                  el: this,
                  duration : data.duration,
                  offset : {
                      'start': data.offset[0],
                      'end': data.offset[1],
                      'distance': data.offset[1]-data.offset[0],
                      'original': {
                        'start': data.offset[0],
                        'end': data.offset[1],
                        'distance': data.offset[1]-data.offset[0]
                      }
                  }
            });
        });


        this.resize();
        this.controller.on('resize', this.resize, this);
        this.controller.on('scroll', this.onScroll, this);

        $layers.addClass('parallax-initialized');
        $layersOpacity.addClass('opacity-initialized');
      }

      /**** onScroll ****/

      this.onScroll = function(scroll){

        if (scroll >= this.scroll.start && scroll <= this.scroll.end) {

              this.updateScene(scroll);
        }

      }

      this.updateScene = function(scroll){
          var advance = scroll - this.scroll.start;

          if(advance < 0){
            advance = 0
          }

          for (var i=0; i<this.scenes.length; i++) {
            var translate = this.scenes[i].offset.start + (advance * this.scenes[i].advanceFactor);
              TweenMax.set(this.scenes[i].el,{y:translate+'%'});

          }
          for (var i=0; i<this.scenesOpacity.length; i++) {
              var opacity = this.scenesOpacity[i].offset.start + (advance * this.scenesOpacity[i].advanceFactor);
              if(opacity >= 1) opacity = 1;
              TweenMax.set(this.scenesOpacity[i].el,{opacity:opacity});
          }
      }

      /**** onResize ****/


      this.resize = function(){

          this.height = this.el.offsetHeight;


          var elTop = this.$el.offset().top-this.controller.$el.offset().top+Viewport.header.height;

          if(elTop < Viewport.height) {
              var distance = elTop + this.height;
          } else if(this.duration == 1){
              var distance = Viewport.height + this.height;
          } else {
              var distance = Viewport.height * this.duration  + this.height/2;
          }

          if(elTop < Viewport.height) {

              var start = 0;

          } else {

              var start = elTop - Viewport.height;
          }


          var end = start + distance;

          this.scroll = {
                'start' : start,
                'distance': distance,
                'end': end
          }


          for (var i=0; i<this.scenes.length; i++) {

              var scene = this.scenes[i];

              if(elTop < Viewport.height) {

                  scene.offset.start = -((100 / this.scroll.distance) * scene.offset.end);

              } else {

                  scene.offset.start = scene.offset.original.start;
              }

              scene.offset.distance = scene.offset.end-scene.offset.start;
              scene.advanceFactor = scene.offset.distance / this.scroll.distance;

          }

          for (var i=0; i<this.scenesOpacity.length; i++) {

              var scene = this.scenesOpacity[i];

              if(elTop < Viewport.height) {

                  scene.offset.start = 0;

              } else {



                  scene.offset.start = scene.offset.original.start;
              }

              scene.offset.distance = scene.offset.end-scene.offset.start;
              scene.advanceFactor = scene.offset.distance / (Viewport.height * scene.duration);


          }

          if(this.controller.isActive()){
              this.updateScene(window.scrollY, true);
          } else {
              this.updateScene(this.controller.props.scroll, true);
          }



      }

      /**** init ****/

      this.init();

}



module.exports = Parallax;
