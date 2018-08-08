'use strict';
var $ = require('jquery');
var Viewport = require('../mixins/viewport');

function ScrollLoad(el, ScrollController) {

      /**** constructor ****/

      this.init = function(){
        var self = this;

        if(el instanceof jQuery){
            this.$el = el.eq(0);

        } else {
          this.$el = $(el).eq(0);
        }

        if (this.$el.data('scroll-lazyload')) return;


        this.el = this.$el.get(0);

        this.controller = ScrollController;

        this.isKilled = false;

        this.$targets = [];

        if(typeof this.$el.data('lazyload-trigger') != 'undefined'){

          var $target = $(this.$el.data('lazyload-trigger'));
          this.$targets.push($target);

          new ScrollLoad($target,ScrollController);

        }

        if(typeof this.$el.data('lazyload') != 'undefined'){
          this.$targets.push(this.$el);
        }


        this.$el.data('scroll-lazyload',this);

        this.resize();
        this.controller.on('resize', this.resize, this);
        this.controller.on('scroll', this.onScroll, this);


      }

      /**** onScroll ****/

      this.onScroll = function(scroll){

        if(this.isKilled || !this.controller.isActive()) return;



        var scroll = parseInt(scroll);


        if (scroll >= this.scroll.start && scroll <=  this.scroll.end ) {

          for (var i=0; i<this.$targets.length; i++) {

              var $el = this.$targets[i];

              $el.removeAttr( 'data-lazyload' );

              if($el.data('scroll-lazyload')){
                $el.data('scroll-lazyload').kill();
              }

          }


          if(!this.isKilled) this.kill();

        }

      }

      /**** onResize ****/


      this.resize = function(){


          var elTop = this.$el.offset().top-(this.controller.$el.offset().top-Viewport.header.height);
          var elBottom = elTop + this.el.offsetHeight+Viewport.header.height;

          var offsetScreen = 1.0;

          var start = parseInt(elTop - Viewport.height * (1+offsetScreen));
          var end = parseInt(elBottom + Viewport.height * offsetScreen);


          if(start < 0){
            start = 0;
          }

          if(end < 0){
            end = 0;
          }

          if(end <= start){
            end = start+2;
          }

          this.scroll = {
                'start' : start,
                'end': end
          }

          if(this.controller.isActive()){
              this.onScroll(window.scrollY);
          } else {
              this.onScroll(this.controller.props.scroll);
          }


      }

      this.kill = function(){
        this.isKilled = true;
        this.controller.off('resize', this.resize);
        this.controller.off('scroll', this.onScroll);

        this.$el.removeData('lazyload');
        this.$el.removeData('scroll-lazyload');
        this.$el.removeAttr( 'data-lazyload-trigger' );
      }

      /**** init ****/

      this.init();

}



module.exports = ScrollLoad;
