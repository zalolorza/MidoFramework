require('magnific-popup');
var $ = require('jquery');

$(function(){
  $('.popup-trigger, .popup-trigger-img').each(function(){
    var $this = $(this);
    var items = $this.data('items');
    $this.magnificPopup({
      items: items,
      gallery: {
        enabled: true
      },
      type: 'image' // this is default type
    });
  });
});
