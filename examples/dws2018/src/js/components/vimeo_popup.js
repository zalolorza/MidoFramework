import $ from 'jquery';
import lity from 'lity';

var VIDEO_STOPPED = false;

$('[data-vimeo]').click(function(e){
    e.preventDefault();
    var $trigger = $(this);
    var $vide_bkg = $trigger.parents('.video-wrapper-parent').find('.video-wrapper');
    if($vide_bkg.length > 0){
        VIDEO_STOPPED = $vide_bkg; 
        VIDEO_STOPPED.data('video').pause();
    } else {
        VIDEO_STOPPED = false;
    }
    lity($trigger.data('vimeo'));
});

$(document).on('lity:open', function(event, instance) {
    $('html').addClass('no-scroll');
}).on('lity:close', function(event, instance) {

    if(VIDEO_STOPPED){
        VIDEO_STOPPED.data('video').play();
        VIDEO_STOPPED = false;
    }
    $('html').removeClass('no-scroll');
});