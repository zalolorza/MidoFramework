import slick from 'slick-carousel';
import $ from 'jquery';

function resizeSliderAbsolute(){
    $('.slide-content-absolute-wrapper').each(function(){
        var $content = $(this).find('.slide-content-absolute');
        $content.height($(this).innerHeight());
        $content.width($(this).innerWidth());
    });
};

resizeSliderAbsolute();

$(window).on('resizing', resizeSliderAbsolute);

$('.slider').each(function(){

    $(this).slick({
        arrows: false,
        dots:true,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: false
    }).click(function(){
        $(this).slick('slickNext');
    });
});



