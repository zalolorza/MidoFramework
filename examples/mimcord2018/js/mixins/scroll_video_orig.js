/***
 * VideoScroller.js
 * URL: https://github.com/finnursig/VideoScroller
 * Author: Finnur Sigurðsson (finnursigu@gmail.com)
 */

// IE10+: window.URL.createObjectURL

class VideoScroller {
  varructor({
    el,
    transitionTime = 2000,
    invert = false,
    scrollTimeout = 300,
    easingFunction = EasingFunctions.easeOutQuint,
    debug = false
  }) {
    if(!el) {
      throw new Error('Missing video element ref.');
    }

    this.el = el;
    this.transitionTime = transitionTime;
    this.invert = invert;
    this.scrollTimeout = scrollTimeout;
    this.easingFunction = typeof easingFunction == "function" ? easingFunction : EasingFunctions[easingFunction];
    this.debug = debug;

    if (!VideoScroller.isCompatibleWithCurrentBrowser) {
      return;
    }

    this.getVideo();
  }



  static get isCompatibleWithCurrentBrowser(){
    if(!window.URL || !window.URL.createObjectURL) {
      return false;
    }

    if (!XMLHttpRequest) {
      return false;
    }

    return true;
  }

  getVideo(){
    var self = this;

    this.req = new XMLHttpRequest();
    this.req.responseType = 'blob';
    req.withCredentials = false;
    this.$video = $(this.el);
    this.video = this.el;
    if(this.video.canPlayType('video/mp4;codecs="avc1.42E01E, mp4a.40.2') == "probably"){
        var src = this.$video.data('mp4');
    } else {
        var src = this.$video.data('webm');
    }

    this.req.onload = function() {
       if (this.status === 200) {
          self.trigger("load");
          self.video.addEventListener('loadeddata', function(){
            self.init()
          );
          self.video.src = URL.createObjectURL(this.response);    // IE10+
       }
    }

    this.req.onprogress = function(requestProgress) {
      var percentage = Math.round(requestProgress.loaded / requestProgress.total * 100);

      if(self.debug)
        console.log('onprogress', percentage + '%');
    };

    this.req.onreadystatechange = function() {
      if (self.debug)
        console.log('onreadystatechange', self.req.readyState);
    };

    this.req.open('GET', src, true);
    this.req.send();

  }

  init() {
    this.videoDuration = this.el.duration;

    if(this.debug){
        this.el.controls = true;
    }

    this.el.className = this.el.className + ' video-scroller-ready';

    window.addEventListener('scroll', function(e){ self.onScroll() }, false);

    this.start(this.inView(this.el));
  }

  start(time) {
    this.startTime = Date.now();

    this.currentTime = this.el.currentTime;
    this.targetDuration = (this.videoDuration * time) - this.el.currentTime;

    if(this.debug) {
        console.log('time=', time,'targetTime=', this.currentTime, 'targetDuration', this.targetDuration);
    }

    if(!this.intervalTimer){
        this.intervalTimer = setInterval(() => this.loop(), 60);
    }
  }

  loop() {
    var i = (Date.now() - this.startTime) / this.transitionTime;
    var easing = this.easingFunction(i);

    if(i >= 1){
        return;
    }

    this.el.currentTime = this.currentTime + this.targetDuration * easing;
    this.el.pause();
  }

  inView() {
    var windowHeight = window.innerHeight;

    var elTop = this.el.getBoundingClientRect().top;
    var elHeight = this.el.offsetHeight;

    let fromTop = elTop - windowHeight;

    if(fromTop > 0){
      fromTop = 0;
    }

    let percentage = Math.abs(fromTop) / (windowHeight + elHeight);

    if(!this.invert){
      percentage = 1-percentage;
    }

    if(percentage > 1){
      return 1;
    } else if(percentage < 0){
      return 0;
    }

    return percentage;
  }

  onScroll() {
    if(this.isWaiting) {
        return;
    }

    this.isWaiting = true;

    setTimeout(() => {
      this.isWaiting = false;

      var time = this.inView(this.el);

      if(time === undefined)
          return;

      this.start(time);
    }, this.scrollTimeout);
  }
}
