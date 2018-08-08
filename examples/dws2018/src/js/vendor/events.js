import _ from 'lodash';
import $ from 'jquery';


var resizing = _.throttle(function(){
  $(window).trigger('resizing');
},300);
$(window).resize(resizing);
$(resizing);


var isResized = _.debounce(function(){
  $(window).trigger('resizing');
  $(window).trigger('isResized');
},500);
$(window).resize(isResized);
$(isResized);



