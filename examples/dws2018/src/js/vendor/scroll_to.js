import { TweenMax } from "gsap";
import ScrollToPlugin from "gsap/ScrollToPlugin";
import $ from 'jquery';

$('*[data-scroll-to]').click(function(e){
    e.preventDefault();
    e.stopPropagation();
    TweenLite.to(window, 0.7, {scrollTo:$(this).data('scroll-to')});
});

