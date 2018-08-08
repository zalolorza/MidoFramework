'use strict';
var $ = require('jquery');


function Viewport() {

      this.$window = $(window);

      this.sizes = [];
      this.events = {};

      this.init = function(){

        for (var i = 0; i < this.sizes.length; i++) {
          this[this.sizes[i]] = {
              el: this.sizes[i],
              $el: $(this.sizes[i])
          }
        }

        this.addElement(['#globalNav']);

        this.events.transitionEnd = this.detectTransitionEndEvent();
        this.events.animationEnd = this.detectAnimationEndEvent();

        this.resize();
        this.listen();
      }

      this.body = {

          height: function(val){
              if (typeof val != 'undefined'){
                  document.body.style.height = val;
              } else {
                return document.body.style.height;
              }

          }

      }



      this.addElement = function(element){

          if(Array.isArray(element)){
            for (var i = 0; i < element.length; i++) {
                this.addElement(element[i]);
            }
            return;
          }

          this.sizes.push(element);
          this[element] = {
              el: element,
              $el: $(element),
              width : $(element).innerWidth(),
              height : $(element).innerHeight()
          }
      }

      this.resize = function(){
          this.width = this.$window.width();
          this.height = this.$window.height();


          for (var i = 0; i < this.sizes.length; i++) {
            var element = this.sizes[i];
            this[element].width = this[element].$el.innerWidth();
            this[element].height = this[element].$el.innerHeight();
          }

          this.header = {
            height : this['#globalNav'].height,
            width  : this['#globalNav'].width,
          }

          if(typeof this['#globalNav'] != 'undefined' && typeof this['#localNav'] != 'undefined'){
              //$('body').css({minHeight:this.height+this['#globalNav'].height+this['#localNav'].height+'px'});
          }


      }


      this.listen = function(){

          var self = this;
          this.$window.resize(function(){
                self.resize();
          });
      }

      this.scrollTop = function(val){
          if(typeof val == 'undefined'){
            var scrollTop = this.$window.scrollTop();
            return scrollTop;
          } else {
            this.$window.scrollTop(val);
          }
      }

      this.detectTransitionEndEvent = function(){
        var t,
            el = document.createElement("fakeelement");

        var transitions = {
          "transition"      : "transitionend",
          "OTransition"     : "oTransitionEnd",
          "MozTransition"   : "transitionend",
          "WebkitTransition": "webkitTransitionEnd"
        }

        for (t in transitions){
          if (el.style[t] !== undefined){
            return transitions[t];
          }
        }
      }

      this.detectAnimationEndEvent = function(){
        var t,
            el = document.createElement("fakeelement");

        var transitions = {
          "animation"      : "animationend",
          "OAnimation"     : "oAnimationEnd",
          "MozAnimation"   : "animationend",
          "WebkitAnimation": "webkitAnimationEnd"
        }

        for (t in transitions){
          if (el.style[t] !== undefined){
            return transitions[t];
          }
        }
      }

      this.init();

}

var viewport = new Viewport();

module.exports = viewport;
