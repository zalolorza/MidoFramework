var $ = require('jquery');
var _ = require('underscore');

var $window = $(window);

$window.on('resize', _.throttle(function(){
    $window.trigger('resizethrottled');
}, 200));