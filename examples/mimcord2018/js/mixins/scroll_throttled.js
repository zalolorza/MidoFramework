var $ = require('jquery');
var _ = require('underscore');

var $window = $(window);

if(Modernizr.touchevents){
    $window.on('scroll', _.throttle(function(){
        $window.trigger('scrollthrottled');
    }, 20));
} else {
    $window.on('scroll', function(){
        $window.trigger('scrollthrottled');
    });
}
