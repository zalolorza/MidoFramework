import $ from 'jquery';
import _ from 'lodash';

var Video = function($el){

    this.$el = $el;

    if(window.mobileAndTabletcheck()){
        this.$el.remove();
        return;
    };

    if(typeof autoplay == 'undefined') var autoplay = true;

    this.width = this.$el.data('width');
    this.height = this.$el.data('height');
    this.$parent = this.$el.parent();
    this.prop = this.height / this.width;
    this.$el.hide().css('opacity',1);
    this.video = this.$el.find('video');
    this.sources =  this.$el.find('source');
    this.hasPlayed = false;

    var self = this;

    
    this.$parent.addClass('video-wrapper-parent');

    this.sources.each(function(){
        $(this).attr('src',$(this).data('src'));
    });

    $(window).on('resizing', function(){
        self.resize();
    });
    
    this.video.on('playing',function(){
        if(!self.hasPlayed){
            if(!autoplay){
                self.video[0].pause();
            }
            self.hasPlayed = true;
            self.show();
            self.$el.trigger('startedPlaying');
            self.$el.trigger('started');
        } 
    });

    

    this.resize = function(){
        var parentWidth = this.$parent.innerWidth();
        var parentHeight = this.$parent.innerHeight();
        var parentProp = parentHeight / parentWidth;
        if(parentProp > this.prop){
            this.$el.height(parentHeight);
            this.$el.width(parentHeight/this.prop);
        } else {
            
            this.$el.width(parentWidth);
            this.$el.height(parentWidth*this.prop);
        }
    }

    this.play = function(){
        
        this.show();
        this.video[0].play();
        this.$el.trigger('play');
    }

    this.pause = function(){
       // this.hide();
        this.video[0].pause();
        this.$el.trigger('pause');
    }

    this.hide = function(){
        var self = this;
        this.$poster.fadeIn();
        self.$el.delay(200).fadeOut();
    }

    this.show = function(){
        var self = this;
        this.$el.fadeIn(500);
    }

    this.resize();
    this.video[0].load();
    
    if(window.mobileAndTabletcheck()){
        
        setTimeout(function(){
            self.pause();
        },500);
        
    } else {

        this.video[0].play();

    }
   
}



$('.video-wrapper').each(function(){

    var $video = new Video($(this));
    $(this).data('video',$video);

});


export default Video;
