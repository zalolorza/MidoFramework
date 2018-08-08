'use strict';
var $ = require('jquery');
var Backbone = require('backbone');
var _ = require('underscore');
var TweenMax = require('gsap/TweenMax');
var Viewport = require('../mixins/viewport');
var Events = require('../mixins/events_map');
var StickyHeader = require('../mixins/scroll_header');

function ScrollController(Scroll, args) {

      /**** constructor ****/

      this.init = function(){

        _.extend(this,Backbone.Events);
        
        this.el = args.el;
        this.$el = $(this.el);
        this.$content = $(this.el).find('.vs-controller-content');

        this.props = (typeof args !== 'undefined') ?  args : {};
        this.props.active = (this.props.active !== 'undefined') ?  this.props.active : false;
        this.props.isMain = (this.props.isMain !== 'undefined') ?  this.props.isMain : false;
        this.props.hasChanged = false;


        this.resize();


        this.Scroll = Scroll;
        this.props.vscroll = 0;

        if(this.props.isMain){
              this.props.className = "main";
              this.$el.addClass('vs-scroll-on');
              this.Scroll.currentController = this;
        } else {
              this.props.className = "overlay";
              this.hide(false);
        }

        this.addListeners();

        this.$el.data('scroll-controller',this);
      }

      /**** listen ****/

      this.addListeners = function(scroll){

          var self = this;

          Events.on('windowIsResized',function(){
              self.resize();
          });

          this.Scroll.addListener('scroll', this.vScrollListener, this);
      }

      this.vScrollListener = function(vscroll){

          if(!this.props.active || this.props.vscroll == vscroll) {
            this.props.hasChanged = false;
            return;
          }
          this.trigger('scroll',vscroll);
          this.props.hasChanged = true;
          this.props.vscroll = vscroll;
      }



      /* listeners */

      /*this.listeners = {'scroll': [],'resize':[]};

      this.on = function(ev, callback, scope){

      		var scope = (typeof scope !== 'undefined') ?  scope : this;
      		this.listeners[ev].push({scope: scope, callback:callback});

      }

      this.off = function(ev, callback){

          console.log('before:'+this.listeners[ev].length);
          this.listeners[ev] = this.listeners[ev].filter(function(el) {
              console.log(el.callback);
              console.log(callback);
              return el.callback != callback;
          });
          console.log('after:'+this.listeners[ev].length);
      }

      this.trigger = function(ev, arg){
      	var listeners = this.listeners[ev];
      	for (var i=0; i<listeners.length; i++) {
      		listeners[i].callback.call(listeners[i].scope, arg);
      	}
      }*/


      /**** methods ****/

      this.get = function(){
         return this.props.scroll;
      }

      this.isActive = function(){
        return this.props.active;
      }

      this.hasChanged = function(){
        return this.props.hasChanged;
      }

      this.show = function(){

          if(this.props.active) return;

          var self = this;

          var realScroll = self.Scroll.getReal();

          this.Scroll.deactivate();
          clearTimeout(this.Scroll.vars.timer);

          this.$el.show();
          this.$el.css({opacity:1});
          this.$closingEl = this.Scroll.currentController.$el;

          this.Scroll.currentController.props.active = false;



          this.Scroll.currentController.hide(function(){

              self.Scroll.currentController = self;
              Viewport.body.height(self.height);
              self.trigger('scroll',self.props.scroll);
              self.$el.removeClass('vs-hide-'+self.props.className);
              self.$el.addClass('vs-show-'+self.props.className);

              if(self.props.isMain){
                setTimeout(function(){self.colorMenu('white')},100);
              }

              self.$el.one(Viewport.events.animationEnd,
                  function(event) {
                      self.afterShow();
                });
          });

          StickyHeader(this.props.scroll);

      }


      this.afterShow = function(){

        var self = this;

        if(!this.props.isMain){
          this.colorMenu('black');
        }

        this.$el.addClass('vs-scroll-on');

        this.Scroll.set(this.props.scroll);

        setTimeout(function(){

          self.Scroll.reactivate();
          self.props.active = true;

        },80);

        Viewport.body.height('auto');
        this.$closingEl.hide();
      }

      this.colorMenu = function(color){
        if(color == 'white'){
          Viewport['#globalNav'].$el.removeClass('header-overlay');
          Viewport['#localNav'].$el.removeClass('header-overlay');
        } else {
          Viewport['#globalNav'].$el.addClass('header-overlay');
          Viewport['#localNav'].$el.addClass('header-overlay');
        }
      }

      this.hide = function(callback){

          var self = this;
          this.props.active = false;

          if(this.props.isMain){
            this.props.scroll = window.scrollY;
          } else {
            self.props.scroll = Viewport.header.height+1;
          }

          if(callback){
                var scroll = window.scrollY;
          } else {
                var scroll = this.props.scroll;
          }

          TweenMax.set(this.$content,{top:(Viewport.header.height-scroll)});

          if (callback){

                this.$el.removeClass('vs-scroll-on');
                this.$el.removeClass('vs-show-'+this.props.className);
                this.$el.addClass('vs-hide-'+this.props.className);

                this.$el.one(Viewport.events.animationEnd,
                    function(event) {
                      if(!self.props.isMain) TweenMax.set(self.$content,{top:-1});
                  });

                callback();
          } else {
                //this.$el.hide();

                if(!self.props.isMain) {
                  TweenMax.set(self.$content,{top:-1});
                }
          }

      }


      this.resize = function(){

          //var isHidden = (this.el.offsetParent == null) ? true : false;
          var isHidden = this.props.active;

          if(isHidden) {
            this.$el.show();
            this.$el.addClass('show-for-resize');
          }
          this.props.height = this.$el.innerHeight();
          this.props.width = this.$el.innerWidth();
          this.trigger('resize');

          if(isHidden) {
            //this.$el.hide();
            this.$el.removeClass('show-for-resize');
          }

      }


      /**** init ****/

      this.init(args);

}



module.exports = ScrollController;
