'use strict';
var $ = require('jquery');
var _ = require('underscore');
var Backbone = require('backbone');
var Scroll = require('../mixins/scroll_controllers');
var EasingFunctions = require('../mixins/easing');
var Viewport = require('../mixins/viewport');
var ScrollController = Scroll.controllers[0];

var VideoScroller = function(args){

  this.defaults = {
    el : null,
    transitionTime : 1500,
    invert : false,
    scrollTimeout : 250,
    easingFunction : EasingFunctions.easeOutQuint,
    debug : false,
    duration: 1
  }

  this.callbacks = {}

  this.on = function(callbackName, callbackFunction){
      if(typeof this.callbacks[callbackName] == 'undefined') this.callbacks[callbackName] = [];
      this.callbacks[callbackName].push(callbackFunction);
      return this;
  }

  this.callback = function(callbackName, args){
    if(typeof this.callbacks[callbackName] == 'undefined') return;
    for (var callbackKey in this.callbacks[callbackName]) {
        this.callbacks[callbackName][callbackKey](args);
    }
  }

  this.construct = function(args){

    if (!this.isCompatibleWithCurrentBrowser) {
      return;
    }


    for (var argKey in this.defaults) {

        this[argKey] = typeof args[argKey] !== 'undefined' ? args[argKey] : this.defaults[argKey];

        switch(argKey){
          case "easingFunction":
            this[argKey] = typeof this[argKey] == "function" ? this[argKey] : EasingFunctions[this[argKey]];
            break;
        }
    }

    return this;

  }

  this.set = function(key,val){
      if(key == 'easingFunction'){
        this.easingFunction = EasingFunctions[val];
      } else {
          this[key] = val;
      }
      return this;
  }

  this.isCompatibleWithCurrentBrowser = function(){
    if(!window.URL || !window.URL.createObjectURL) {
      return false;
    }

    if (!XMLHttpRequest) {
      return false;
    }

    return true;
  }

  this.init = function(){
      this.isInitialized = true;
      if(this.isLoaded) this.toStartingPoint();
  }
  this.load = function(){
    var self = this;

    var req = new XMLHttpRequest();
    req.responseType = 'blob';
    req.withCredentials = false;
    this.$video = $(this.el);
    this.video = this.el;
    if(this.video.canPlayType('video/mp4;codecs="avc1.42E01E, mp4a.40.2') == "probably"){
        var src = this.$video.data('mp4');
    } else {
        var src = this.$video.data('webm');
    }

    req.onload = function() {
       if (this.status === 200) {
          self.video.addEventListener('loadeddata', function(){
            self.isLoaded = true;
            self.callback('load');
            if(self.isInitialized) self.toStartingPoint();
          });
          self.video.src = URL.createObjectURL(this.response);    // IE10+
          self.video.pause();
       }
    }

    req.onprogress = function(requestProgress) {
      var percentage = Math.round(requestProgress.loaded / requestProgress.total * 100);

      if(self.debug)
        console.log('onprogress', percentage + '%');
    };

    req.onreadystatechange = function() {
      if (self.debug)
        console.log('onreadystatechange', req.readyState);
    };

    req.open('GET', src, true);
    req.send();
  }

  this.toStartingPoint = function() {

    var self = this;

    var self = this;
    this.videoDuration = this.el.duration;

    if(this.debug){
        this.el.controls = true;
    }

    this.el.className = this.el.className + ' video-scroller-ready';

    window.addEventListener('scroll', function(){self.onScroll()}, false);
    window.addEventListener('resize', function(){_.debounce(self.onResize, 300, false)}, false);
    this.onResize();
    this.frameTime = 1/30;
    this.previousTime = 0;
    this.start(this.inView(this.el));
    this.callback('start');
    setTimeout(function(){
        self.callback('startingPoint')
    }, this.transitionTime)
  }

  this.start = function(time) {

    if(time == this.previousTime) return false;

    var self = this;

    this.startTime = Date.now();
    this.previousTime = time;
    this.currentTime = this.el.currentTime;

    this.targetDuration = (this.videoDuration * time) - this.el.currentTime;

    if(this.debug) {
        console.log('time=', time,'targetTime=', this.currentTime, 'targetDuration', this.targetDuration);
    }

    if(!this.intervalTimer){
        this.intervalTimer = setInterval(function(){self.loop()}, 40);
    }
  }

  this.loop = function() {

    var now = Date.now();

    var i = (now - this.startTime) / this.transitionTime;
    if(i >= 1){
        return;
    }

    var easing = this.easingFunction(i);
    var time = this.targetDuration * easing;

    if(Math.abs(this.targetDuration-time) > this.frameTime){
      this.el.currentTime = this.currentTime + time;
    }
    //this.callback('during',this.el.currentTime);
    this.el.pause();
  }

  this.inView = function() {

    if(!ScrollController.isActive()) return;

    var fromTop = this.el.getBoundingClientRect().top - this.windowHeight;

    if(fromTop > 0){
      fromTop = 0;
    }


    var percentage = Math.abs(fromTop) / this.elBottom;

    if(this.invert){
      percentage = 1-percentage;
    }

    if(percentage > 1){
      return 1;
    } else if(percentage < 0){
      return 0;
    }

    return percentage;
  }

  this.onScroll = function() {

    var self = this;

    if(this.isWaiting) {
        return;
    }

    this.isWaiting = true;

    setTimeout(function() {
      self.isWaiting = false;

      var time = self.inView(self.el);

      if(time === undefined)
          return;

      self.start(time);

    }, this.scrollTimeout);
  }

  this.onResize = function(){


    this.windowHeight = Viewport.height;
    this.elHeight = this.el.offsetHeight;
    if(this.duration == 1){
        this.elBottom = Viewport.height + this.el.offsetHeight;
    } else {
        this.elBottom = Viewport.height*this.duration  + this.el.offsetHeight/2;
    }


  }

  this.construct(args);
};

module.exports = VideoScroller;
