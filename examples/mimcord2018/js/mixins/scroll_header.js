'use strict';
var Viewport = require('../mixins/viewport');
var $ = require('jquery');

var stickyHeader = function(scroll){
      if((scroll > Viewport['#globalNav'].height && !Viewport['#localNav'].isFixed) ||
        (scroll <= Viewport['#globalNav'].height && Viewport['#localNav'].isFixed) ) {

        Viewport['#localNav'].isFixed = !Viewport['#localNav'].isFixed;

        if(Viewport['#localNav'].isFixed) {
            Viewport['#localNav'].$el.addClass('nav-fixed');
            Viewport['#globalNav'].$el.addClass('nav-fixed');
        } else {
            Viewport['#localNav'].$el.removeClass('nav-fixed');
            Viewport['#globalNav'].$el.removeClass('nav-fixed');
        }
    }
}

module.exports = stickyHeader;
