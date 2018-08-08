'use strict';
var $ = require('jquery');
var Viewport = require('../mixins/viewport');

function InView(props, ScrollController) {

      /**** constructor ****/

      this.init = function(){
        var self = this;

        if(props.el instanceof jQuery){
            this.$el = props.el.eq(0);

        } else {
          this.$el = $(props.el).eq(0);
        }
        this.el = this.$el.get(0);

        this.controller = ScrollController;

        if(typeof props.offset == 'undefined'){
            this.offset = this.$el.data('offset');
        } else {
          this.offset = props.offset;
        }

        this.props = props;



        this.$el.removeClass('in-view');
        this.inView = false;

        if(typeof this.props.onLeaveBottom == 'function') this.props.onLeaveBottom(this.el);
        this.resize();
        this.controller.on('resize', this.resize, this);
        this.controller.on('scroll', this.onScroll, this);

      }

      /**** onScroll ****/

      this.onScroll = function(scroll){

        var scroll = parseInt(scroll);

        if (scroll >= this.scroll.start && !this.inView) {
              this.$el.addClass('in-view');
              this.inView = true;
              if(typeof this.props.onEnterBottom == 'function') this.props.onEnterBottom(this.el);
        }

        if (scroll < this.scroll.start && this.inView) {
              this.$el.removeClass('in-view');
              this.inView = false;
              if(typeof this.props.onLeaveBottom == 'function') this.props.onLeaveBottom(this.el);
        }

      }

      /**** onResize ****/


      this.resize = function(){


          var elTop = this.$el.offset().top-(this.controller.$el.offset().top-Viewport.header.height);
          var elBottom = elTop + this.el.offsetHeight+Viewport.header.height;
          var elTop = elTop + this.offset*this.el.offsetHeight;


          var start = elTop - Viewport.height;
          var end = elBottom;



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

      /**** init ****/

      this.init();

}



module.exports = InView;
